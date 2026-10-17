<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends ApiController
{
    protected BookingService $bookingService;

    // Dependency Injection
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * API 2: Kiểm tra các ghế người dùng chọn có còn trống không trước khi thanh toán
     * Endpoint: POST /api/matches/{id}/check-seats
     * Body JSON: { "seat_ids": [1, 2, 3] }
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function checkSeats(Request $request, $id): JsonResponse
    {
        $seatIds = $request->input('seat_ids');

        // Kiểm tra tính hợp lệ cơ bản của dữ liệu gửi lên
        if (!is_array($seatIds) || empty($seatIds)) {
            return new JsonResponse([
                'success'      => false,
                'is_available' => false,
                'message'      => 'Dữ liệu không hợp lệ. Vui lòng gửi mảng seat_ids.',
            ], 422); // HTTP 422: Dữ liệu không hợp lệ
        }

        // Gọi tầng Service kiểm tra
        $result = $this->bookingService->checkSeatsAvailability((int)$id, $seatIds);

        // Trường hợp có ghế đã bị người khác mua mất (Conflict)
        if (!$result['is_available'] && !empty($result['locked_seats'])) {
            return new JsonResponse($result, 409); // HTTP 409: Tranh chấp tài nguyên
        }

        // Trường hợp lỗi khác (không tìm thấy ghế/trận)
        if (!$result['success']) {
            return new JsonResponse($result, 404);
        }

        // Thành công: Tất cả ghế đều trống -> HTTP 200 OK
        return new JsonResponse($result, 200);
    }

    /**
     * API 3: giữ ghế trong một đơn hàng PENDING có thời hạn.
     * POST /api/matches/{id}/hold-seats
     */
    public function holdSeats(Request $request, $id): JsonResponse
    {
        $user = $this->requireAuthenticatedUser($request);
        if ($user instanceof JsonResponse) return $user;

        $seatIds = $request->input('seat_ids');
        $requestedUserId = $request->input('user_id');
        $duration = $request->input('hold_duration_minutes', 10);
        if (!is_array($seatIds) || $seatIds === [] || filter_var($duration, FILTER_VALIDATE_INT) === false) {
            return $this->response(false, 'seat_ids phải là mảng không rỗng và hold_duration_minutes phải là số nguyên.', null, 422);
        }
        if ($requestedUserId !== null && (int) $requestedUserId !== (int) $user->id) {
            return $this->response(false, 'Không được giữ ghế thay cho tài khoản khác.', null, 403);
        }

        return $this->serviceResponse($this->bookingService->holdSeats((int) $user->id, (int) $id, $seatIds, (int) $duration));
    }
}
