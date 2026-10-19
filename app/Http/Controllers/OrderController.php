<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function __construct(private BookingService $bookingService) {}

    /** DELETE /api/orders/{orderId} */
    public function cancel(Request $request, int $orderId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        $requestedUserId = $request->input('user_id');
        if ($requestedUserId !== null && (int) $requestedUserId !== (int) $user->id) return $this->response(false, 'Không được hủy đơn thay cho tài khoản khác.', null, 403);
        // is_admin in the body is deliberately ignored; role is determined by the authenticated user.
        return $this->serviceResponse($this->bookingService->cancelOrder($orderId, (int) $user->id, $user->isAdmin()));
    }

    /** GET /api/orders/{orderId}, including tickets and payment. */
    public function show(Request $request, int $orderId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        $order = Order::query()->with(['tickets.match', 'tickets.matchSeat.seatZone', 'payment'])->find($orderId);
        if (!$order) return $this->response(false, 'Không tìm thấy đơn hàng.', null, 404);
        if (!$user->isAdmin() && (int) $order->user_id !== (int) $user->id) return $this->response(false, 'Bạn không có quyền truy cập đơn hàng này.', null, 403);
        return $this->response(true, 'Lấy chi tiết đơn hàng thành công.', $order);
    }

    /** GET /api/users/{userId}/orders */
    public function indexForUser(Request $request, int $userId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        if (!$user->isAdmin() && (int) $user->id !== $userId) return $this->response(false, 'Bạn không có quyền xem đơn hàng của người dùng khác.', null, 403);
        $orders = Order::query()->where('user_id', $userId)->with(['tickets.match', 'tickets.matchSeat.seatZone', 'payment'])->orderByDesc('id')->get();
        return $this->response(true, 'Lấy danh sách đơn hàng thành công.', $orders);
    }
}
