<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
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
            return response()->json([
                'success'      => false,
                'is_available' => false,
                'message'      => 'Dữ liệu không hợp lệ. Vui lòng gửi mảng seat_ids.',
            ], 422); // HTTP 422: Dữ liệu không hợp lệ
        }

        // Gọi tầng Service kiểm tra
        $result = $this->bookingService->checkSeatsAvailability((int)$id, $seatIds);

        // Trường hợp có ghế đã bị người khác mua mất (Conflict)
        if (!$result['is_available'] && !empty($result['locked_seats'])) {
            return response()->json($result, 409); // HTTP 409: Tranh chấp tài nguyên
        }

        // Trường hợp lỗi khác (không tìm thấy ghế/trận)
        if (!$result['success']) {
            return response()->json($result, 404);
        }

        // Thành công: Tất cả ghế đều trống -> HTTP 200 OK
        return response()->json($result, 200);
    }
}
