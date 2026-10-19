<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\MatchGame;
use App\Models\MatchSeat;
use App\Models\SeatZone;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MatchController extends ApiController
{
    /**
     * GET /admin/matches
     * Query: ?status=&date=
     */
    public function index(Request $request): View
    {
        $query = MatchGame::query()
            ->with(['homeTeam', 'awayTeam', 'stadium'])
            ->orderBy('match_time', 'DESC');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('match_time', $request->query('date'));
        }

        $matches = $query->paginate(15)->withQueryString();
        $teams = Team::query()->orderBy('name')->get();
        $stadiums = Stadium::query()->orderBy('name')->get();

        return view('admin.matches.index', compact('matches', 'teams', 'stadiums'));
    }

    /**
     * GET /admin/matches/create
     */
    public function create(): View
    {
        $teams = Team::query()->orderBy('name')->get();
        $stadiums = Stadium::query()->orderBy('name')->get();
        return view('admin.matches.create', compact('teams', 'stadiums'));
    }

    /**
     * GET /admin/matches/{id}/edit
     */
    public function edit(int $id): View
    {
        $match = MatchGame::with(['homeTeam', 'awayTeam', 'stadium'])->findOrFail($id);
        return view('admin.matches.edit', compact('match'));
    }

    /**
     * POST /admin/matches
     * Tạo lịch thi đấu mới và tự động sinh 500 ghế theo các khu khán đài của sân
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'home_team_id' => 'required|different:away_team_id|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id',
            'stadium_id'   => 'required|exists:stadiums,id',
            'match_time'   => 'required|date',
            'stage'        => 'required|string|max:50',
            'match_number' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request) {
            $matchNumber = $request->input('match_number') ?: (MatchGame::max('match_number') + 1);

            $match = MatchGame::create([
                'match_number' => $matchNumber,
                'stage'        => $request->input('stage'),
                'stadium_id'   => (int) $request->input('stadium_id'),
                'home_team_id' => (int) $request->input('home_team_id'),
                'away_team_id' => (int) $request->input('away_team_id'),
                'match_time'   => $request->input('match_time'),
                'match_date'   => $request->input('match_time'),
                'status'       => 'UPCOMING',
                'match_status' => 'UPCOMING',
            ]);

            // Lấy các khu vực ghế của sân để sinh ghế
            $zones = SeatZone::query()->where('stadium_id', $match->stadium_id)->get();
            if ($zones->isEmpty()) {
                // Nếu sân chưa có zones, lấy default zones của sân 1
                $zones = SeatZone::query()->where('stadium_id', 1)->get();
            }

            $seatInserts = [];
            foreach ($zones as $zone) {
                $prefix = strtoupper(substr($zone->name ?: 'A', 0, 1));
                if (!in_array($prefix, ['A', 'B', 'C', 'D'])) $prefix = 'A';
                $count = min((int) ($zone->total_seats ?: 100), 150);

                for ($i = 1; $i <= $count; $i++) {
                    $seatInserts[] = [
                        'match_id'     => $match->id,
                        'seat_zone_id' => $zone->id,
                        'seat_code'    => "{$prefix}{$i}",
                        'price'        => $zone->base_price ?: 1500000,
                        'status'       => 'AVAILABLE',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
            }

            // Insert theo chunk để tối ưu hiệu năng
            foreach (array_chunk($seatInserts, 100) as $chunk) {
                MatchSeat::insert($chunk);
            }
        });

        return redirect('/admin/matches')->with('success', 'Đã tạo trận đấu mới và tự động sinh danh sách ghế thành công.');
    }

    /**
     * PUT /admin/matches/{id}
     * Cập nhật thông tin, tỷ số hoặc trạng thái trận đấu
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $match = MatchGame::findOrFail($id);

        $request->validate([
            'status'     => 'required|in:UPCOMING,LIVE,FINISHED,CANCELLED',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'match_time' => 'required|date',
        ]);

        $match->update([
            'status'       => $request->input('status'),
            'match_status' => $request->input('status'),
            'home_score'   => $request->input('home_score'),
            'away_score'   => $request->input('away_score'),
            'match_time'   => $request->input('match_time'),
            'match_date'   => $request->input('match_time'),
        ]);

        return redirect('/admin/matches')->with('success', 'Cập nhật trận đấu thành công.');
    }

    /**
     * DELETE /admin/matches/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $match = MatchGame::findOrFail($id);
        
        DB::transaction(function () use ($match) {
            MatchSeat::query()->where('match_id', $match->id)->delete();
            $match->delete();
        });

        return redirect('/admin/matches')->with('success', 'Đã hủy/xóa trận đấu và giải phóng các ghế liên quan.');
    }
}
