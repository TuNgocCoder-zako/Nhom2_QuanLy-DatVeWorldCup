<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\City;
use App\Models\SeatZone;
use App\Models\Stadium;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StadiumController extends ApiController
{
    /**
     * GET /admin/stadiums
     */
    public function index(): View
    {
        $stadiums = Stadium::query()
            ->with(['city.hostCountry', 'seatZones'])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $cities = City::query()->orderBy('city_name')->get();

        return view('admin.stadiums.index', compact('stadiums', 'cities'));
    }

    /**
     * GET /admin/stadiums/create
     */
    public function create(): View
    {
        return view('admin.stadiums.create');
    }

    /**
     * GET /admin/stadiums/{id}/edit
     */
    public function edit(int $id): View
    {
        $stadium = Stadium::findOrFail($id);
        return view('admin.stadiums.edit', compact('stadium'));
    }

    /**
     * POST /admin/stadiums
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'city_id'  => 'nullable|integer',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1000',
        ]);

        $stadium = Stadium::create([
            'name'         => $request->input('name'),
            'stadium_name' => $request->input('name'),
            'city_id'      => $request->input('city_id') ?: 1,
            'location'     => $request->input('location'),
            'capacity'     => (int) $request->input('capacity'),
        ]);

        // Mặc định mỗi sân có 300 ghế VIP và 700 ghế phổ thông.
        $defaultZones = [
            ['name' => 'Khán đài VIP', 'zone_name' => 'Khán đài VIP', 'type' => 'VIP', 'total_seats' => 300, 'base_price' => 8500000],
            ['name' => 'Khán đài Phổ Thông', 'zone_name' => 'Khán đài Phổ Thông', 'type' => 'STANDARD', 'total_seats' => 700, 'base_price' => 2500000],
        ];

        foreach ($defaultZones as $zone) {
            SeatZone::create(array_merge($zone, ['stadium_id' => $stadium->id]));
        }

        return redirect('/admin/stadiums')->with('success', 'Đã thêm sân vận động với 300 ghế VIP và 700 ghế phổ thông.');
    }

    /**
     * PUT /admin/stadiums/{id}
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $stadium = Stadium::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:150',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1000',
        ]);

        $stadium->update([
            'name'         => $request->input('name'),
            'stadium_name' => $request->input('name'),
            'location'     => $request->input('location'),
            'capacity'     => (int) $request->input('capacity'),
        ]);

        return redirect('/admin/stadiums')->with('success', 'Cập nhật thông tin sân vận động thành công.');
    }

    /**
     * DELETE /admin/stadiums/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $stadium = Stadium::findOrFail($id);
        $stadium->delete();

        return redirect('/admin/stadiums')->with('success', 'Đã xóa sân vận động khỏi hệ thống.');
    }
}
