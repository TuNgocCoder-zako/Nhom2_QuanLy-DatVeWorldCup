<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\MatchSeat;
use App\Services\TicketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends ApiController
{
    public function __construct(private TicketService $ticketService) {}

    /** GET /api/users/{userId}/tickets */
    public function indexForUser(Request $request, int $userId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        if (!$user->isAdmin() && (int) $user->id !== $userId) {
            return $this->response(false, 'Bạn không có quyền xem vé của người dùng khác.', null, 403);
        }
        $perPage = (int) $request->query('per_page', 15);
        $result = $this->ticketService->getUserTickets($userId, ['per_page' => $perPage]);
        return $this->serviceResponse($result);
    }

    /** GET /api/tickets/{code} */
    public function show(Request $request, string $code): JsonResponse
    {
        $result = $this->ticketService->getTicketDetailByCode($code);
        return $this->serviceResponse($result);
    }

    /**
     * POST /api/tickets/{code}/verify
     * Quét mã QR soát vé cổng.
     * Response: JSON {valid: bool, ticket, match}
     */
    public function verify(Request $request, string $code): JsonResponse
    {
        $ticket = Ticket::query()
            ->with([
                'order:id,user_id,order_code,status',
                'match:id,match_number,stage,stadium_id,home_team_id,away_team_id,match_time,status',
                'match.homeTeam:id,name,team_name,code,flag_url',
                'match.awayTeam:id,name,team_name,code,flag_url',
                'match.stadium:id,name,stadium_name,location',
                'matchSeat:id,seat_zone_id,seat_code,status',
                'matchSeat.seatZone:id,zone_name,name,type'
            ])
            ->where('ticket_code', $code)
            ->first();

        if (!$ticket) {
            return response()->json([
                'valid' => false,
                'message' => 'Không tìm thấy vé trong hệ thống.',
                'ticket' => null,
                'match' => null,
            ], 404);
        }

        if ($ticket->status !== 'VALID') {
            return response()->json([
                'valid' => false,
                'message' => 'Vé không hợp lệ hoặc đã bị hủy (Trạng thái: ' . $ticket->status . ').',
                'ticket' => $ticket,
                'match' => $ticket->match,
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Xác thực vé thành công! Cho phép vào sân.',
            'ticket' => $ticket,
            'match' => $ticket->match,
        ], 200);
    }

    /**
     * GET /my-tickets
     * Xem danh sách vé đã mua của người dùng hiện tại
     */
    public function myTickets(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        if (!$user) {
            return redirect('/login')->with('warning', 'Vui lòng đăng nhập để xem vé của bạn.');
        }

        $tickets = Ticket::query()
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                'match.homeTeam',
                'match.awayTeam',
                'match.stadium',
                'matchSeat.seatZone',
                'order'
            ])
            ->orderByDesc('id')
            ->get();

        return view('user.tickets.my-tickets', compact('tickets', 'user'));
    }

    /**
     * GET /my-tickets/{id}/pdf
     * Tải vé điện tử định dạng PDF kèm mã QR
     */
    public function downloadPdf(Request $request, int $id): Response|\Illuminate\Http\RedirectResponse|JsonResponse
    {
        $ticket = Ticket::query()
            ->with([
                'order.user',
                'match.homeTeam',
                'match.awayTeam',
                'match.stadium',
                'matchSeat.seatZone'
            ])
            ->find($id);

        if (!$ticket) {
            if ($request->wantsJson()) {
                return $this->response(false, 'Không tìm thấy vé.', null, 404);
            }
            return back()->with('error', 'Không tìm thấy vé để xuất PDF.');
        }

        // Tạo chuỗi SVG cho QR code
        $qrCodeSvg = QrCode::format('svg')
            ->size(140)
            ->margin(1)
            ->generate($ticket->ticket_code);

        $pdf = Pdf::loadView('tickets.ticket-pdf', [
            'ticket' => $ticket,
            'qrCodeSvg' => (string) $qrCodeSvg,
        ])->setPaper('a5', 'landscape');

        return $pdf->download("ticket_{$ticket->id}.pdf");
    }

    /**
     * POST /my-tickets/{id}/cancel
     * Hủy vé khi đủ điều kiện
     */
    public function cancelTicket(Request $request, int $id): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        if (!$user) {
            if ($request->wantsJson()) return $this->response(false, 'Chưa đăng nhập.', null, 401);
            return redirect('/login');
        }

        $ticket = Ticket::query()->with('order')->find($id);
        if (!$ticket) {
            if ($request->wantsJson()) return $this->response(false, 'Không tìm thấy vé.', null, 404);
            return back()->with('error', 'Không tìm thấy vé.');
        }

        if (!$user->isAdmin() && (int) $ticket->order->user_id !== (int) $user->id) {
            if ($request->wantsJson()) return $this->response(false, 'Bạn không có quyền thao tác trên vé này.', null, 403);
            return back()->with('error', 'Bạn không có quyền thao tác trên vé này.');
        }

        if ($ticket->status === 'CANCELLED') {
            if ($request->wantsJson()) return $this->response(false, 'Vé đã bị hủy trước đó.', null, 400);
            return back()->with('warning', 'Vé đã bị hủy trước đó.');
        }

        $ticket->status = 'CANCELLED';
        $ticket->save();

        // Trả ghế về trạng thái AVAILABLE
        if ($ticket->match_seat_id) {
            MatchSeat::query()->where('id', $ticket->match_seat_id)->update(['status' => 'AVAILABLE']);
        }

        if ($request->wantsJson()) {
            return $this->response(true, 'Đã hủy vé thành công.', $ticket);
        }

        return back()->with('success', 'Đã hủy vé thành công và hoàn trả ghế.');
    }
}
