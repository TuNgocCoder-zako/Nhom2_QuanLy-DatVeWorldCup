<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Stadium;
use App\Models\Team;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * GET /matches
     * Danh sách lịch đấu & bộ lọc (?date=&team=&stadium=)
     */
    public function index(Request $request): View
    {
        $query = MatchGame::query()
            ->with(['homeTeam', 'awayTeam', 'stadium.city'])
            ->orderBy('match_time', 'ASC');

        if ($request->filled('date')) {
            $query->whereDate('match_time', $request->query('date'));
        }

        if ($request->filled('team')) {
            $teamId = (int) $request->query('team');
            $query->where(function ($q) use ($teamId) {
                $q->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId);
            });
        }

        if ($request->filled('stadium')) {
            $query->where('stadium_id', (int) $request->query('stadium'));
        }

        $matches = $query->paginate(12)->withQueryString();
        $teams = Team::query()->orderBy('name')->get();
        $stadiums = Stadium::query()->orderBy('name')->get();

        return view('user.matches.index', compact('matches', 'teams', 'stadiums'));
    }

    /**
     * GET /matches/{id}
     * Chi tiết trận đấu & sơ đồ chọn ghế trực quan
     */
    public function show(int $id): View
    {
        $match = MatchGame::query()
            ->with(['homeTeam.players', 'awayTeam.players', 'stadium.seatZones', 'stadium.city'])
            ->findOrFail($id);

        return view('user.matches.show', compact('match'));
    }

    /**
     * API 1: Lấy thông tin trận đấu và sơ đồ ghế
     * Endpoint: GET /api/matches/{id}/seats
     */
    public function getSeats(Request $request, $id): JsonResponse
    {
        $filters = [
            'limit' => $request->query('limit', 500),
        ];

        $result = $this->bookingService->getMatchSeatsMap((int)$id, $filters);

        if (!$result['success']) {
            return new JsonResponse($result, 404);
        }

        return new JsonResponse($result, 200);
    }
}
