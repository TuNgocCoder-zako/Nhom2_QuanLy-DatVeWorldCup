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
     * Endpoint: POST /api/matches/{id}/check-seats hoặc POST /api/check-seats
     * Body JSON: { "seat_ids": [1, 2, 3] } hoặc { "match_id": 1, "seat_ids": [1, 2] }
     *
     * @param Request $request
     * @param int|string|null $id
     * @return JsonResponse
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
     * Body JSON: { "user_id": 2, "seat_ids": [1, 2] } hoặc { "match_id": 1, "user_id": 2, "seat_ids": [1, 2] }
     *
     * @param Request $request
     * @param int|string|null $id
     * @return JsonResponse
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
     * Chuẩn hoá danh sách seat_ids từ nhiều định dạng khác nhau (mảng, chuỗi JSON, chuỗi phân cách dấu phẩy, giá trị đơn).
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
