<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends ApiController
{
    /**
     * GET /admin/teams
     * Query: ?search=&group=
     */
    public function index(Request $request): View
    {
        $query = Team::query()->orderBy('group_name')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('group_name', $request->query('group'));
        }

        $teams = $query->paginate(16)->withQueryString();
        $groups = ['A','B','C','D','E','F','G','H','I','J','K','L'];

        return view('admin.teams.index', compact('teams', 'groups'));
    }

    /**
     * GET /admin/teams/create
     */
    public function create(): View
    {
        $groups = ['A','B','C','D','E','F','G','H','I','J','K','L'];
        return view('admin.teams.create', compact('groups'));
    }

    /**
     * GET /admin/teams/{id}/edit
     */
    public function edit(int $id): View
    {
        $team = Team::findOrFail($id);
        $groups = ['A','B','C','D','E','F','G','H','I','J','K','L'];
        return view('admin.teams.edit', compact('team', 'groups'));
    }

    /**
     * POST /admin/teams
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => 'required|string|size:3|unique:teams,code',
            'group_name' => 'required|in:A,B,C,D,E,F,G,H,I,J,K,L',
            'flag_url'   => 'nullable|url|max:255',
        ]);

        Team::create([
            'name'       => $request->input('name'),
            'team_name'  => $request->input('name'),
            'code'       => strtoupper($request->input('code')),
            'group_name' => $request->input('group_name'),
            'flag_url'   => $request->input('flag_url') ?: 'https://flagcdn.com/w80/' . strtolower(substr($request->input('code'), 0, 2)) . '.png',
        ]);

        return redirect('/admin/teams')->with('success', 'Đã thêm đội tuyển mới thành công.');
    }

    /**
     * PUT /admin/teams/{id}
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $team = Team::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:100',
            'code'       => "required|string|size:3|unique:teams,code,{$id}",
            'group_name' => 'required|in:A,B,C,D,E,F,G,H,I,J,K,L',
            'flag_url'   => 'nullable|string|max:255',
        ]);

        $team->update([
            'name'       => $request->input('name'),
            'team_name'  => $request->input('name'),
            'code'       => strtoupper($request->input('code')),
            'group_name' => $request->input('group_name'),
            'flag_url'   => $request->input('flag_url'),
        ]);

        return redirect('/admin/teams')->with('success', 'Cập nhật thông tin đội tuyển thành công.');
    }

    /**
     * DELETE /admin/teams/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect('/admin/teams')->with('success', 'Đã xóa đội tuyển khỏi hệ thống.');
    }
}
