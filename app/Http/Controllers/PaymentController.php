<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function __construct(private PaymentService $paymentService) {}

    /** POST /api/orders/{orderId}/payment */
    public function create(Request $request, int $orderId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        if ($denied = $this->authorizeOrder($orderId, $user->id, $user->isAdmin())) return $denied;

        $method = $request->input('payment_method');
        if (!is_string($method) || trim($method) === '') return $this->response(false, 'payment_method là bắt buộc.', null, 422);
        return $this->serviceResponse($this->paymentService->createPaymentTransaction($orderId, $method));
    }

    /** POST /api/orders/{orderId}/payment/simulate */
    public function simulate(Request $request, int $orderId): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;
        if ($denied = $this->authorizeOrder($orderId, $user->id, $user->isAdmin())) return $denied;
        return $this->serviceResponse($this->paymentService->simulatePayment($orderId));
    }

    /** POST /api/orders/{orderId}/payment/success - gateway callback after signature validation by caller/middleware. */
    public function success(Request $request, int $orderId): JsonResponse
    {
        $transactionId = $request->input('transaction_id');
        $method = $request->input('payment_method');
        if (!is_string($transactionId) || trim($transactionId) === '' || !is_string($method) || trim($method) === '') {
            return $this->response(false, 'transaction_id và payment_method là bắt buộc.', null, 422);
        }
        return $this->serviceResponse($this->paymentService->processPaymentSuccess($orderId, $transactionId, $method));
    }

    /** POST /api/orders/{orderId}/payment/failure - gateway callback after signature validation by caller/middleware. */
    public function failure(Request $request, int $orderId): JsonResponse
    {
        $reason = $request->input('reason');
        if (!is_string($reason) || trim($reason) === '') return $this->response(false, 'reason là bắt buộc.', null, 422);
        return $this->serviceResponse($this->paymentService->processPaymentFailure($orderId, $reason));
    }

    private function authorizeOrder(int $orderId, int $userId, bool $isAdmin): ?JsonResponse
    {
        $order = Order::query()->find($orderId);
        if (!$order) return $this->response(false, 'Không tìm thấy đơn hàng.', null, 404);
        if (!$isAdmin && (int) $order->user_id !== $userId) return $this->response(false, 'Bạn không có quyền truy cập đơn hàng này.', null, 403);
        return null;
    }
}
