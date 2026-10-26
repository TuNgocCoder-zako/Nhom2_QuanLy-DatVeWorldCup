@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng & Vé - FIFA World Cup 2030')
@section('page_title', 'Danh Sách Đơn Đặt Vé')

@section('content')
<div class="admin-card mb-4">
    <div class="p-3">
        <form action="{{ url('/admin/orders') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm theo mã đơn hoặc tên/email khách..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm rounded-pill px-3">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Đã thanh toán (PAID)</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Chờ thanh toán (PENDING)</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Đã hủy (CANCELLED)</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fa-solid fa-search me-1"></i> Tìm kiếm</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ url('/admin/orders') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-rotate-left"></i> Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Số lượng vé</th>
                        <th>Tổng tiền</th>
                        <th>Phương thức</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($orders as $order)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">#{{ $order->order_code ?? $order->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $order->user->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">{{ $order->tickets->count() }} vé</span>
                        </td>
                        <td class="fw-bold text-danger font-display">
                            {{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} đ
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-light text-secondary border px-2 py-1">{{ $order->payment_method ?? 'VNPAY' }}</span>
                        </td>
                        <td>
                            @if($order->status === 'PAID')
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <i class="fa-solid fa-check-circle me-1"></i>PAID
                                </span>
                            @elseif($order->status === 'CANCELLED')
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">
                                    CANCELLED
                                </span>
                            @else
                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                    <i class="fa-solid fa-clock me-1"></i>PENDING
                                </span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Xem chi tiết đơn và vé">
                                <i class="fa-solid fa-eye me-1"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>
@endsection
