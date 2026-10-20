<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\MatchSeat;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends ApiController
{
    /**
     * GET /admin/orders
     * Query: ?status=&search=
     */
    public function index(Request $request): View
    {
        $query = Order::query()
            ->with(['user', 'tickets.matchSeat'])
            ->orderBy('id', 'DESC');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * GET /admin/orders/{id}
     */
    public function show(int $id): View
    {
        $order = Order::query()
            ->with([
                'user',
                'tickets.match.homeTeam',
                'tickets.match.awayTeam',
                'tickets.match.stadium',
                'tickets.matchSeat.seatZone',
            ])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * POST /admin/orders/{id}/cancel
     * Hủy đơn hàng và giải phóng ghế cấp quản trị
     */
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::with('tickets')->findOrFail($id);

        if ($order->status === 'CANCELLED') {
            return back()->with('warning', 'Đơn hàng này đã bị hủy trước đó.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status'         => 'CANCELLED',
                'payment_status' => 'CANCELLED',
            ]);

            // Cập nhật trạng thái vé thành CANCELLED
            Ticket::query()->where('order_id', $order->id)->update(['status' => 'CANCELLED']);

            // Giải phóng các ghế đã đặt về AVAILABLE
            $seatIds = $order->tickets->pluck('match_seat_id')->filter()->all();
            if (!empty($seatIds)) {
                MatchSeat::query()->whereIn('id', $seatIds)->update(['status' => 'AVAILABLE']);
            }
        });

        return back()->with('success', "Đã hủy đơn hàng #{$order->order_code} và giải phóng các vị trí ghế về trạng thái trống.");
    }
}
