<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * GET /
     * Trang chủ World Cup 2030 (Trận hot, bảng đấu, sân vận động nổi bật)
     */
    public function index(): View
    {
        // 4 trận đấu hot sắp diễn ra
        $hotMatches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam', 'stadium.city'])
            ->where('status', 'UPCOMING')
            ->orderBy('match_time', 'ASC')
            ->take(4)
            ->get();

        if ($hotMatches->isEmpty()) {
            $hotMatches = MatchGame::query()
                ->with(['homeTeam', 'awayTeam', 'stadium.city'])
                ->orderBy('match_time', 'ASC')
                ->take(4)
                ->get();
        }

        // Danh sách các đội tuyển phân theo bảng đấu A -> L
        $teamsByGroup = Team::query()
            ->orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        // Danh sách các sân vận động nổi bật
        $featuredStadiums = Stadium::query()
            ->with('city')
            ->orderByDesc('capacity')
            ->take(4)
            ->get();

        return view('user.home', compact('hotMatches', 'teamsByGroup', 'featuredStadiums'));
    }
}
