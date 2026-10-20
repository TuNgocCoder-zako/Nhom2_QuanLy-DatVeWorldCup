<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\MatchGame;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends ApiController
{
    /**
     * GET /admin/dashboard
     * Hiển thị bảng điều khiển thống kê tổng quan
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $admin = $this->authenticatedUser($request);
        if (!$admin || !$admin->isAdmin()) {
            return redirect('/login')->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        $totalRevenue = (float) Order::query()->where('status', 'PAID')->sum('total_amount');
        $totalTicketsSold = Ticket::query()->where('status', 'VALID')->count();
        $totalMatches = MatchGame::query()->count();
        $totalUsers = User::query()->where('role', 'USER')->count();

        $recentOrders = Order::query()
            ->with('user')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $upcomingMatches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam', 'stadium'])
            ->where('status', 'UPCOMING')
            ->orderBy('match_time')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'admin',
            'totalRevenue',
            'totalTicketsSold',
            'totalMatches',
            'totalUsers',
            'recentOrders',
            'upcomingMatches'
        ));
    }

    /**
     * GET /api/admin/dashboard/chart
     * Dữ liệu biểu đồ doanh thu theo ngày hoặc tháng
     * Query: ?type=daily|monthly
     * Response: JSON {labels: [], revenues: []}
     */
    public function chart(Request $request): JsonResponse
    {
        $type = $request->query('type', 'daily');

        if ($type === 'monthly') {
            $data = Order::query()
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as time_label"),
                    DB::raw("SUM(COALESCE(total_amount, total_price, 0)) as revenue")
                )
                ->where('status', 'PAID')
                ->groupBy('time_label')
                ->orderBy('time_label', 'ASC')
                ->take(12)
                ->get();
        } else {
            $data = Order::query()
                ->select(
                    DB::raw("DATE(created_at) as time_label"),
                    DB::raw("SUM(COALESCE(total_amount, total_price, 0)) as revenue")
                )
                ->where('status', 'PAID')
                ->groupBy('time_label')
                ->orderBy('time_label', 'ASC')
                ->take(30)
                ->get();
        }

        $labels = $data->pluck('time_label')->toArray();
        $revenues = $data->pluck('revenue')->map(fn($v) => (float) $v)->toArray();

        // Nếu database chưa có đơn PAID mẫu nào, cung cấp fallback mốc dữ liệu để biểu đồ hiển thị đẹp
        if (empty($labels)) {
            if ($type === 'monthly') {
                $labels = ['2030-01', '2030-02', '2030-03', '2030-04', '2030-05', '2030-06'];
                $revenues = [0, 0, 0, 0, 0, 0];
            } else {
                $labels = [date('Y-m-d', strtotime('-4 days')), date('Y-m-d', strtotime('-3 days')), date('Y-m-d', strtotime('-2 days')), date('Y-m-d', strtotime('-1 day')), date('Y-m-d')];
                $revenues = [0, 0, 0, 0, 0];
            }
        }

        return response()->json([
            'labels' => $labels,
            'revenues' => $revenues,
        ]);
    }
}
