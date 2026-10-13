<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    protected BookingService $bookingService;

    // Dependency Injection: Tương đương @Autowired BookingService trong Spring
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * API 1: Lấy thông tin trận đấu và sơ đồ ghế
     * Endpoint: GET /api/matches/{id}/seats
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function getSeats(Request $request, $id): JsonResponse
    {
        $filters = [
            'limit' => $request->query('limit', 500),
        ];

        // Gọi tầng Service xử lý nghiệp vụ
        $result = $this->bookingService->getMatchSeatsMap((int)$id, $filters);

        // Nếu không tìm thấy trận đấu -> HTTP 404 Not Found
        if (!$result['success']) {
            return response()->json($result, 404);
        }

        // Thành công -> HTTP 200 OK
        return response()->json($result, 200);
    }
}
