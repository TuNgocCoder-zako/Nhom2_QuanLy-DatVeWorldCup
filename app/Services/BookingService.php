<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\MatchSeat;
use App\Models\SeatZone;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Nghiệp vụ API 1: Lấy thông tin trận đấu và sơ đồ ghế
     *
     * @param int $matchId
     * @param array $filters
     * @return array
     */
    public function getMatchSeatsMap(int $matchId, array $filters = []): array
    {
        // 1. Tìm trận đấu kèm thông tin 2 đội và sân vận động (Eager Loading chống N+1)
        $match = MatchGame::with([
            'homeTeam:id,name,code',
            'awayTeam:id,name,code',
            'stadium:id,name,location,capacity'
        ])->find($matchId);

        if (!$match) {
            return [
                'success' => false,
                'message' => "Không tìm thấy trận đấu với ID: {$matchId}",
                'data'    => null,
            ];
        }

        // 2. Lấy danh sách khán đài của sân này
        $zones = SeatZone::where('stadium_id', $match->stadium_id)->get();

        // 3. Lấy danh sách ghế của trận đấu (mặc định giới hạn để render mượt mà)
        $limit = $filters['limit'] ?? 500;
        $seats = MatchSeat::where('match_id', $matchId)
            ->limit((int)$limit)
            ->get(['id', 'seat_zone_id', 'seat_code', 'price', 'status']);

        // 4. Thống kê nhanh số lượng ghế theo trạng thái
        $seatStats = MatchSeat::where('match_id', $matchId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 5. Đóng gói dữ liệu trả về (tương đương tầng DTO)
        return [
            'success' => true,
            'message' => 'Lấy sơ đồ ghế thành công',
            'data'    => [
                'match' => [
                    'id'           => $match->id,
                    'match_number' => $match->match_number,
                    'stage'        => $match->stage,
                    'match_time'   => $match->match_time,
                    'status'       => $match->status,
                    'home_team'    => $match->homeTeam,
                    'away_team'    => $match->awayTeam,
                    'stadium'      => $match->stadium,
                ],
                'stats' => [
                    'total'     => array_sum($seatStats),
                    'available' => $seatStats['AVAILABLE'] ?? 0,
                    'booked'    => $seatStats['BOOKED'] ?? 0,
                ],
                'zones' => $zones,
                'seats' => $seats,
            ]
        ];
    }

    /**
     * Nghiệp vụ API 2: Kiểm tra các ghế người dùng chọn có còn trống không
     *
     * @param int $matchId
     * @param array $seatIds Mảng chứa các ID ghế người dùng chọn
     * @return array
     */
    public function checkSeatsAvailability(int $matchId, array $seatIds): array
    {
        $seatIds = array_values(array_unique(array_map('intval', $seatIds)));

        if (empty($seatIds)) {
            return [
                'success'      => false,
                'is_available' => false,
                'message'      => 'Vui lòng chọn ít nhất 1 chiếc ghế.',
                'locked_seats' => [],
                'data'         => null,
            ];
        }

        // 1. Lấy thông tin các ghế được chọn thuộc đúng trận đấu này
        $seats = MatchSeat::where('match_id', $matchId)
            ->whereIn('id', $seatIds)
            ->get();

        // 2. Kiểm tra nếu có ID ghế không tồn tại hoặc không thuộc trận này
        if ($seats->count() !== count($seatIds)) {
            return [
                'success'      => false,
                'is_available' => false,
                'message'      => 'Một số ghế bạn chọn không tồn tại hoặc không thuộc trận đấu này.',
                'locked_seats' => [],
                'data'         => null,
            ];
        }

        // 3. Lọc ra các ghế KHÔNG CÒN TRỐNG (Trạng thái khác AVAILABLE)
        $lockedSeats = $seats->filter(function ($seat) {
            return $seat->status !== 'AVAILABLE';
        });

        // Nếu phát hiện có ghế đã bị mua trước -> Báo lỗi tranh chấp
        if ($lockedSeats->isNotEmpty()) {
            return [
                'success'      => false,
                'is_available' => false,
                'message'      => 'Một hoặc nhiều ghế bạn chọn vừa có người đặt trước.',
                'locked_seats' => $lockedSeats->map(function ($seat) {
                    return [
                        'id'        => $seat->id,
                        'seat_code' => $seat->seat_code,
                        'status'    => $seat->status,
                    ];
                })->values()->toArray(),
                'data' => null,
            ];
        }

        // 4. Tất cả các ghế đều còn trống -> Tính tổng tiền
        $totalPrice = $seats->sum('price');

        return [
            'success'      => true,
            'is_available' => true,
            'message'      => 'Tất cả các ghế đều còn trống, sẵn sàng để đặt vé.',
            'locked_seats' => [],
            'data'         => [
                'match_id'       => $matchId,
                'selected_count' => $seats->count(),
                'total_price'    => (float)$totalPrice,
                'seats'          => $seats->map(function ($seat) {
                    return [
                        'id'        => $seat->id,
                        'seat_code' => $seat->seat_code,
                        'price'     => (float)$seat->price,
                    ];
                })->values()->toArray(),
            ]
        ];
    }
}
