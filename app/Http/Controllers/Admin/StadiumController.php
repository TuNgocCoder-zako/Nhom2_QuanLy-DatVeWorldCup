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

        // Tạo sẵn các khu vực khán đài tiêu chuẩn A, B, C, D cho sân mới
        $defaultZones = [
            ['name' => 'Khán đài A (VIP)', 'zone_name' => 'Khán đài A (VIP)', 'type' => 'VIP', 'total_seats' => 50, 'base_price' => 5000000],
            ['name' => 'Khán đài B (Standard)', 'zone_name' => 'Khán đài B (Standard)', 'type' => 'STANDARD', 'total_seats' => 150, 'base_price' => 2500000],
            ['name' => 'Khán đài C (Standard)', 'zone_name' => 'Khán đài C (Standard)', 'type' => 'STANDARD', 'total_seats' => 150, 'base_price' => 1800000],
            ['name' => 'Khán đài D (Standard)', 'zone_name' => 'Khán đài D (Standard)', 'type' => 'STANDARD', 'total_seats' => 150, 'base_price' => 1200000],
        ];

        foreach ($defaultZones as $zone) {
            SeatZone::create(array_merge($zone, ['stadium_id' => $stadium->id]));
        }

        return redirect('/admin/stadiums')->with('success', 'Đã thêm sân vận động và tạo 4 khu vực khán đài mặc định.');
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
