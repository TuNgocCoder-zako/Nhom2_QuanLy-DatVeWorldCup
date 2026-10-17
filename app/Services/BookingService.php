<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MatchGame;
use App\Models\MatchSeat;
use App\Models\Order;
use App\Models\SeatZone;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/** Coordinates seat holds and order lifecycle. */
class BookingService
{
    /** @param array<string, mixed> $filters @return array{success:bool,message:string,data:mixed,code:int} */
    public function getMatchSeatsMap(int $matchId, array $filters = []): array
    {
        $match = MatchGame::query()->with(['homeTeam:id,name,code', 'awayTeam:id,name,code', 'stadium:id,name,stadium_name,location,capacity'])
            ->find($matchId);
        if (!$match) return $this->result(false, 'Không tìm thấy trận đấu.', null, 404);

        $zones = SeatZone::query()->where('stadium_id', $match->stadium_id)->get();
        $seatQuery = MatchSeat::query()->where('match_id', $matchId)->select(['id', 'match_id', 'seat_zone_id', 'seat_code', 'price', 'status']);
        if (!empty($filters['zone_id'])) $seatQuery->where('seat_zone_id', (int) $filters['zone_id']);
        if (!empty($filters['status'])) $seatQuery->where('status', strtoupper((string) $filters['status']));
        $limit = max(1, min((int) ($filters['limit'] ?? 500), 5000));
        $seats = $seatQuery->orderBy('seat_zone_id')->orderBy('seat_code')->limit($limit)->get();
        $stats = MatchSeat::query()->where('match_id', $matchId)->select('status', DB::raw('COUNT(*) AS total'))->groupBy('status')->pluck('total', 'status');

        return $this->result(true, 'Lấy sơ đồ ghế thành công.', [
            'match' => $match, 'zones' => $zones, 'seats' => $seats,
            'stats' => ['total' => (int) $stats->sum(), 'available' => (int) ($stats['AVAILABLE'] ?? 0), 'held' => (int) ($stats['HELD'] ?? 0), 'booked' => (int) ($stats['BOOKED'] ?? 0)],
        ]);
    }

    /** @param list<int> $seatIds @return array{success:bool,message:string,data:mixed,code:int} */
    public function bookSeats(int $userId, int $matchId, array $seatIds): array
    {
        $seatIds = array_values(array_unique(array_map('intval', $seatIds)));
        if (!$seatIds || min($seatIds) < 1) return $this->result(false, 'Vui lòng chọn ít nhất một ghế hợp lệ.', null, 400);

        try {
            $order = DB::transaction(function () use ($userId, $matchId, $seatIds): Order {
                $seats = MatchSeat::query()->where('match_id', $matchId)->whereIn('id', $seatIds)->orderBy('id')->lockForUpdate()->get();
                if ($seats->count() !== count($seatIds)) throw new \DomainException('Một số ghế không tồn tại hoặc không thuộc trận đấu này.');
                if ($seats->contains(fn (MatchSeat $seat): bool => $seat->status !== 'AVAILABLE')) throw new \DomainException('Một hoặc nhiều ghế vừa được người khác đặt.');

                $totalCents = $seats->sum(fn (MatchSeat $seat) => (int) round((float) $seat->price * 100));
                $total = number_format($totalCents / 100, 2, '.', '');
                $order = Order::query()->create([
                    'user_id' => $userId, 'order_code' => 'WC-' . Str::upper(Str::random(20)),
                    'total_price' => $total, 'total_amount' => $total, 'status' => 'PENDING', 'payment_status' => 'PENDING',
                    'payment_method' => 'SIMULATION', 'hold_expires_at' => null,
                ]);
                foreach ($seats as $seat) {
                    Ticket::query()->create(['order_id' => $order->id, 'match_id' => $matchId, 'match_seat_id' => $seat->id,
                        'ticket_code' => 'TICKET-' . Str::upper(Str::random(24)), 'price' => $seat->price, 'status' => 'BOOKED']);
                    $seat->status = 'BOOKED';
                    $seat->save();
                }
                return $order->load('tickets');
            }, 3);

            return $this->result(true, 'Đặt ghế thành công, đơn hàng đang chờ thanh toán.', ['order' => $order]);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (Throwable $exception) {
            Log::error('Unable to book match seats.', ['match_id' => $matchId, 'user_id' => $userId, 'exception' => $exception]);
            return $this->result(false, 'Không thể đặt ghế lúc này. Vui lòng thử lại.', null, 500);
        }
    }

    /** Compatibility alias for legacy holdSeats callers. */
    public function holdSeats(int $userId, int $matchId, array $seatIds, int $holdDurationMinutes = 10): array
    {
        return $this->bookSeats($userId, $matchId, $seatIds);
    }

    /** Giữ method này để các tác vụ định kỳ/admin không bị lỗi; tính năng hết hạn giữ ghế đã được tắt. */
    public function releaseExpiredHolds(): array
    {
        return $this->result(true, 'Chức năng giữ ghế có thời hạn đã được tắt. Không có đơn hàng nào bị hủy.', ['released_orders' => 0]);
    }

    /** Cancel an unpaid order owned by the user, or any unpaid order for an admin. */
    public function cancelOrder(int $orderId, int $userId, bool $isAdmin = false): array
    {
        try {
            $order = DB::transaction(function () use ($orderId, $userId, $isAdmin): Order {
                $order = Order::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order) throw new \OutOfBoundsException('Không tìm thấy đơn hàng.');
                if (!$isAdmin && (int) $order->user_id !== $userId) throw new \DomainException('Bạn không có quyền hủy đơn hàng này.');
                if ($order->status !== 'PENDING') throw new \DomainException('Chỉ có thể hủy đơn hàng đang chờ thanh toán.');
                $this->cancelLockedOrder($order);
                return $order->fresh();
            }, 3);
            return $this->result(true, 'Đã hủy đơn hàng và giải phóng ghế.', $order);
        } catch (\DomainException $exception) {
            return $this->result(false, $exception->getMessage(), null, 409);
        } catch (\OutOfBoundsException $exception) {
            return $this->result(false, $exception->getMessage(), null, 404);
        } catch (Throwable $exception) {
            Log::error('Unable to cancel order.', ['order_id' => $orderId, 'exception' => $exception]);
            return $this->result(false, 'Không thể hủy đơn hàng lúc này.', null, 500);
        }
    }

    /** @param list<int> $seatIds @return array{success:bool,message:string,data:mixed,code:int} */
    public function checkSeatsAvailability(int $matchId, array $seatIds): array
    {
        $seatIds = array_values(array_unique(array_map('intval', $seatIds)));
        if (!$seatIds) return $this->availabilityResult(false, 'Vui lòng chọn ít nhất một ghế.', ['is_available' => false, 'locked_seats' => []], [], 400);
        $seats = MatchSeat::query()->where('match_id', $matchId)->whereIn('id', $seatIds)->get(['id', 'seat_code', 'price', 'status']);
        if ($seats->count() !== count($seatIds)) return $this->availabilityResult(false, 'Một số ghế không tồn tại hoặc không thuộc trận đấu này.', ['is_available' => false, 'locked_seats' => []], [], 404);
        $locked = $seats->where('status', '!=', 'AVAILABLE')->map(fn (MatchSeat $seat) => ['id' => $seat->id, 'seat_code' => $seat->seat_code, 'status' => $seat->status])->values();
        return $this->availabilityResult($locked->isEmpty(), $locked->isEmpty() ? 'Các ghế đang còn trống.' : 'Một hoặc nhiều ghế không còn trống.', [
            'is_available' => $locked->isEmpty(), 'locked_seats' => $locked,
            'match_id' => $matchId, 'selected_count' => $seats->count(), 'total_price' => $seats->sum(fn (MatchSeat $seat) => (float) $seat->price), 'seats' => $seats,
        ], $locked->values()->all(), $locked->isEmpty() ? 200 : 409);
    }

    private function cancelLockedOrder(Order $order): void
    {
        $tickets = Ticket::query()->where('order_id', $order->id)->get(['id', 'match_seat_id']);
        $seatIds = $tickets->pluck('match_seat_id')->all();
        if ($seatIds) MatchSeat::query()->whereIn('id', $seatIds)->whereIn('status', ['HELD', 'BOOKED'])->lockForUpdate()->update(['status' => 'AVAILABLE']);
        Ticket::query()->where('order_id', $order->id)->delete();
        $order->status = 'CANCELLED';
        $order->payment_status = 'CANCELLED';
        $order->save();
    }

    /** @return array{success:bool,message:string,data:mixed,code:int} */
    private function result(bool $success, string $message, mixed $data = null, int $code = 200): array
    {
        return compact('success', 'message', 'data', 'code');
    }

    /** Preserve the existing check-seats endpoint's top-level response keys. */
    private function availabilityResult(bool $success, string $message, mixed $data, array $lockedSeats, int $code): array
    {
        return $this->result($success, $message, $data, $code) + [
            'is_available' => (bool) ($data['is_available'] ?? false),
            'locked_seats' => $lockedSeats,
        ];
    }
}
