<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\TicketConfirmationMail;
use App\Models\MatchSeat;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/** Issues and validates tickets. */
class TicketService
{
    /** Activate provisional seat records after a successful payment. */
    public function generateTicketsForOrder(Order $order): array
    {
        try {
            $tickets = DB::transaction(function () use ($order) {
                $lockedOrder = Order::query()->whereKey($order->getKey())->lockForUpdate()->first();
                if (!$lockedOrder || $lockedOrder->status !== 'PAID') throw new \DomainException('Chỉ tạo vé cho đơn đã thanh toán.');
                $tickets = Ticket::query()->where('order_id', $lockedOrder->id)->orderBy('id')->lockForUpdate()->get();
                if ($tickets->isEmpty()) throw new \DomainException('Đơn hàng không có ghế để phát hành vé.');
                foreach ($tickets as $ticket) {
                    if ($ticket->status === 'CANCELLED') throw new \DomainException('Không thể phát hành vé đã hủy.');
                    if (str_starts_with((string) $ticket->ticket_code, 'HOLD-') || str_starts_with((string) $ticket->ticket_code, 'TICKET-')) $ticket->ticket_code = (string) Str::uuid();
                    $ticket->status = 'VALID';
                    $ticket->save();
                }
                return $tickets->fresh();
            }, 3);
            return $this->result(true, 'Đã phát hành vé cho đơn hàng.', $tickets);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (Throwable $exception) {
            Log::error('Unable to issue tickets.', ['order_id' => $order->getKey(), 'exception' => $exception]);
            return $this->result(false, 'Không thể phát hành vé lúc này.', null, 500);
        }
    }

    /** @param array<string,mixed> $filters @return array{success:bool,message:string,data:mixed,code:int} */
    public function getUserTickets(int $userId, array $filters = []): array
    {
        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));
        $query = Ticket::query()->valid()->whereHas('order', fn ($orders) => $orders->where('user_id', $userId)->where('status', 'PAID'))
            ->with(['match:id,match_number,stage,stadium_id,home_team_id,away_team_id,match_time,status',
                'match.homeTeam:id,name,code,flag_url', 'match.awayTeam:id,name,code,flag_url', 'match.stadium:id,name,stadium_name,location',
                'matchSeat:id,seat_zone_id,seat_code', 'matchSeat.seatZone:id,zone_name,name'])
            ->orderByDesc('id');
        if (!empty($filters['status'])) $query->where('status', strtoupper((string) $filters['status']));
        $tickets = $query->paginate($perPage);
        return $this->result(true, 'Lấy danh sách vé thành công.', $tickets);
    }

    public function getTicketDetailByCode(string $ticketCode): array
    {
        $ticket = Ticket::query()->with(['order:id,user_id,order_code,status',
            'match:id,match_number,stage,stadium_id,home_team_id,away_team_id,match_time,status',
            'match.homeTeam:id,name,team_name,code,flag_url', 'match.awayTeam:id,name,team_name,code,flag_url',
            'match.stadium:id,name,stadium_name,location', 'matchSeat:id,seat_zone_id,seat_code', 'matchSeat.seatZone:id,zone_name,name'])
            ->where('ticket_code', $ticketCode)->where('status', 'VALID')->first();
        return $ticket ? $this->result(true, 'Tìm thấy vé.', $ticket) : $this->result(false, 'Không tìm thấy vé.', null, 404);
    }

    /** Validate and consume a ticket once; requires check_in_at schema column (added below). */
    public function validateAndCheckIn(string $ticketCode): array
    {
        try {
            $ticket = DB::transaction(function () use ($ticketCode): Ticket {
                $ticket = Ticket::query()->where('ticket_code', $ticketCode)->lockForUpdate()->first();
                if (!$ticket) throw new ModelNotFoundException();
                if ($ticket->status !== 'VALID') throw new \DomainException('Vé không hợp lệ hoặc đã được sử dụng.');
                $match = $ticket->match()->first();
                if (!$match || $match->status === 'CANCELLED') throw new \DomainException('Trận đấu không còn hợp lệ.');
                if ($match->match_time && now()->lt($match->match_time->copy()->subHours(6))) throw new \DomainException('Chưa đến khung giờ cho phép vào sân.');
                if ($match->match_time && now()->gt($match->match_time->copy()->addHours(3))) throw new \DomainException('Đã quá thời gian vào sân của trận đấu.');
                if ($ticket->check_in_at !== null) throw new \DomainException('Vé này đã được check-in trước đó.');
                $ticket->check_in_at = now();
                $ticket->save();
                return $ticket->fresh()->load(['match.stadium', 'matchSeat.seatZone']);
            }, 3);
            return $this->result(true, 'Check-in thành công.', $ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->result(false, 'Không tìm thấy vé.', null, 404);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (Throwable $exception) {
            Log::error('Ticket check-in failed.', ['ticket_code' => $ticketCode, 'exception' => $exception]);
            return $this->result(false, 'Không thể xác thực vé lúc này.', null, 500);
        }
    }

    public function sendTicketEmail(int $orderId): array
    {
        try {
            $order = Order::query()->with(['user', 'tickets.match.homeTeam', 'tickets.match.awayTeam', 'tickets.match.stadium', 'tickets.matchSeat.seatZone'])
                ->whereKey($orderId)->first();
            if (!$order) return $this->result(false, 'Không tìm thấy đơn hàng.', null, 404);
            if ($order->status !== 'PAID' || $order->tickets->isEmpty()) return $this->result(false, 'Đơn hàng chưa có vé hợp lệ để gửi.', null, 409);
            if (!$order->user || !$order->user->email) return $this->result(false, 'Đơn hàng không có địa chỉ email nhận vé.', null, 400);
            Mail::to($order->user->email)->send(new TicketConfirmationMail($order));
            return $this->result(true, 'Đã gửi email vé điện tử.', ['order_id' => $orderId]);
        } catch (Throwable $exception) {
            Log::error('Unable to email tickets.', ['order_id' => $orderId, 'exception' => $exception]);
            return $this->result(false, 'Không thể gửi email vé lúc này.', null, 500);
        }
    }

    /** @return array{success:bool,message:string,data:mixed,code:int} */
    private function result(bool $success, string $message, mixed $data = null, int $code = 200): array
    {
        return compact('success', 'message', 'data', 'code');
    }
}
