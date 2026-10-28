<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\MatchSeat;
use App\Models\Order;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends ApiController
{
    protected BookingService $bookingService;
    protected PaymentService $paymentService;
    protected TicketService $ticketService;

    public function __construct(BookingService $bookingService, PaymentService $paymentService, TicketService $ticketService)
    {
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
        $this->ticketService = $ticketService;
    }

    /**
     * API 2: Kiểm tra các ghế người dùng chọn có còn trống không trước khi thanh toán
     * Endpoint: POST /api/matches/{id}/check-seats hoặc POST /api/check-seats
     */
    public function checkSeats(Request $request, $id = null): JsonResponse
    {
        $matchId = (int) ($id ?? $request->input('match_id', 0));
        if ($matchId <= 0) {
            return new JsonResponse([
                'success'      => false,
                'is_available' => false,
                'message'      => 'Vui lòng cung cấp mã trận đấu (match_id) hợp lệ.',
            ], 422);
        }

        $seatIds = $this->parseSeatIds($request);
        if (empty($seatIds)) {
            return new JsonResponse([
                'success'      => false,
                'is_available' => false,
                'message'      => 'Dữ liệu không hợp lệ. Vui lòng gửi mảng seat_ids (ví dụ: [1, 2]).',
            ], 422);
        }

        $result = $this->bookingService->checkSeatsAvailability($matchId, $seatIds);

        if (!$result['is_available'] && !empty($result['locked_seats'])) {
            return new JsonResponse($result, 409);
        }

        if (!$result['success']) {
            return new JsonResponse($result, $result['code'] ?? 404);
        }

        return new JsonResponse($result, 200);
    }

    /**
     * API 3: Đặt ghế trực tiếp tạo đơn hàng chờ thanh toán.
     * Endpoint: POST /api/matches/{id}/book-seats hoặc POST /api/book-seats
     */
    public function bookSeats(Request $request, $id = null): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;

        $matchId = (int) ($id ?? $request->input('match_id', 0));
        if ($matchId <= 0) {
            return $this->response(false, 'Vui lòng cung cấp mã trận đấu (match_id) hợp lệ.', null, 422);
        }

        $seatIds = $this->parseSeatIds($request);
        if (empty($seatIds)) {
            return $this->response(false, 'seat_ids phải là mảng không rỗng (ví dụ: [1, 2]).', null, 422);
        }

        $requestedUserId = $request->input('user_id');
        if ($requestedUserId !== null && (int) $requestedUserId !== (int) $user->id) {
            return $this->response(false, 'Không được đặt ghế thay cho tài khoản khác.', null, 403);
        }

        return $this->serviceResponse($this->bookingService->bookSeats((int) $user->id, $matchId, $seatIds));
    }

    /**
     * Tương thích ngược: POST /api/matches/{id}/hold-seats hoặc POST /api/hold-seats
     */
    public function holdSeats(Request $request, $id = null): JsonResponse
    {
        return $this->bookSeats($request, $id);
    }

    /**
     * POST /booking/checkout
     * Chuyển ghế sang màn hình xác nhận thông tin và thanh toán
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        if (!$user) {
            return redirect('/login')->with('warning', 'Vui lòng đăng nhập để tiến hành đặt vé.');
        }

        $matchId = (int) $request->input('match_id');
        $seatIds = $this->parseSeatIds($request);

        if ($matchId <= 0 || empty($seatIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một ghế hợp lệ.');
        }

        $match = MatchGame::query()
            ->with(['homeTeam', 'awayTeam', 'stadium.city'])
            ->find($matchId);

        if (!$match) {
            return redirect('/matches')->with('error', 'Trận đấu không tồn tại.');
        }

        $seats = MatchSeat::query()
            ->with('seatZone')
            ->where('match_id', $matchId)
            ->whereIn('id', $seatIds)
            ->where('status', 'AVAILABLE')
            ->get();

        if ($seats->count() !== count($seatIds)) {
            return back()->with('error', 'Một số ghế bạn chọn vừa được người khác đặt hoặc không còn khả dụng.');
        }

        $totalAmount = $seats->sum('price');

        return view('user.booking.checkout', compact('match', 'seats', 'totalAmount', 'user', 'seatIds'));
    }

    /**
     * POST /booking/payment
     * Xử lý thanh toán mô phỏng từ form đặt vé
     */
    public function processPayment(Request $request): RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        if (!$user) {
            return redirect('/login')->with('warning', 'Vui lòng đăng nhập để thanh toán.');
        }

        $matchId = (int) $request->input('match_id');
        $seatIds = $this->parseSeatIds($request);
        $paymentMethod = $request->input('payment_method', 'VNPAY');

        if ($matchId <= 0 || empty($seatIds)) {
            return redirect('/matches')->with('error', 'Thông tin đặt vé không hợp lệ.');
        }

        // 1. Tạo đơn hàng và vé
        $bookResult = $this->bookingService->bookSeats((int) $user->id, $matchId, $seatIds);
        if (!$bookResult['success']) {
            return redirect("/matches/{$matchId}")->with('error', $bookResult['message']);
        }

        $order = $bookResult['data']['order'];

        // 2. Tạo giao dịch và mô phỏng hoàn tất thanh toán thành công
        $paymentTx = $this->paymentService->createPaymentTransaction((int) $order->id, $paymentMethod);
        if ($paymentTx['success'] && isset($paymentTx['data'])) {
            $this->paymentService->processPaymentSuccess(
                (int) $order->id,
                (string) $paymentTx['data']->transaction_id,
                $paymentMethod
            );
        } else {
            $this->paymentService->simulatePayment((int) $order->id);
        }

        // 3. Tự động gửi email vé điện tử cho người dùng (nếu có lỗi SMTP thì vẫn không làm gián đoạn đặt vé)
        $this->ticketService->sendTicketEmail((int) $order->id);

        return redirect("/booking/success?order_id={$order->id}")
            ->with('success', 'Thanh toán thành công! Vé điện tử của bạn đã sẵn sàng.');
    }

    /**
     * GET /booking/success
     * Màn hình thông báo đặt vé và thanh toán thành công
     */
    public function success(Request $request): View|RedirectResponse
    {
        $orderId = (int) $request->query('order_id');
        if ($orderId <= 0) {
            return redirect('/my-tickets');
        }

        $order = Order::query()
            ->with([
                'tickets.match.homeTeam',
                'tickets.match.awayTeam',
                'tickets.match.stadium',
                'tickets.matchSeat.seatZone',
                'user'
            ])
            ->find($orderId);

        if (!$order) {
            return redirect('/my-tickets');
        }

        return view('user.booking.success', compact('order'));
    }

    /**
     * Chuẩn hoá danh sách seat_ids từ nhiều định dạng khác nhau.
     */
    private function parseSeatIds(Request $request): array
    {
        $seatIds = $request->input('seat_ids', $request->input('seat_id'));
        if (is_string($seatIds)) {
            $decoded = json_decode($seatIds, true);
            if (is_array($decoded)) {
                $seatIds = $decoded;
            } else {
                $seatIds = explode(',', $seatIds);
            }
        } elseif (is_numeric($seatIds)) {
            $seatIds = [$seatIds];
        }

        if (!is_array($seatIds)) {
            return [];
        }

        $parsed = [];
        foreach ($seatIds as $sid) {
            if (is_numeric($sid) && (int) $sid > 0) {
                $parsed[] = (int) $sid;
            }
        }
        return array_values(array_unique($parsed));
    }
}
