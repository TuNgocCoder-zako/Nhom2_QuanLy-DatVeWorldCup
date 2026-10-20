@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . ($order->order_code ?? $order->id))
@section('page_title', 'Chi Tiết Đơn Hàng #' . ($order->order_code ?? $order->id))

@section('content')
<div class="mb-3">
    <a href="{{ url('/admin/orders') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách đơn hàng
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Order & Tickets -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-ticket text-warning me-2"></i> Danh Sách Vé Trong Đơn ({{ $order->tickets->count() }} vé)
                </h6>
                <span class="badge {{ $order->status === 'PAID' ? 'bg-success' : ($order->status === 'CANCELLED' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                    {{ $order->status }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3">Mã vé</th>
                                <th>Trận đấu</th>
                                <th>Vị trí ghế</th>
                                <th>Giá vé</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($order->tickets as $ticket)
                            <tr>
                                <td class="ps-3 fw-bold">
                                    <code>{{ $ticket->ticket_code }}</code>
                                </td>
                                <td>
                                    <strong>{{ $ticket->match->homeTeam->name ?? 'Team A' }} vs {{ $ticket->match->awayTeam->name ?? 'Team B' }}</strong>
                                    <div class="text-muted small">{{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân chính') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $ticket->matchSeat->seat_code ?? 'N/A' }}</span>
                                    <span class="badge bg-info text-dark ms-1">{{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }}</span>
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ number_format((float)$ticket->price, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    <span class="badge {{ $ticket->status === 'VALID' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ url('/my-tickets/' . $ticket->id . '/pdf') }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Xem vé PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Customer Info & Management Action -->
    <div class="col-lg-4">
        <!-- Customer Details -->
        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user me-2 text-primary"></i> Thông Tin Khách Hàng
                </h6>
            </div>
            <div class="card-body p-3 small">
                <div class="mb-2">
                    <span class="text-muted">Họ và tên:</span>
                    <strong class="d-block text-dark">{{ $order->user->name ?? 'Khách vãng lai' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Email:</span>
                    <strong class="d-block text-dark">{{ $order->user->email ?? 'Chưa cập nhật' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Số điện thoại:</span>
                    <strong class="d-block text-dark">{{ $order->user->phone ?? 'Chưa cập nhật' }}</strong>
                </div>
                <div class="mb-2 pt-2 border-top">
                    <span class="text-muted">Phương thức thanh toán:</span>
                    <strong class="d-block text-primary">{{ $order->payment_method ?? 'VNPAY' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Tổng thanh toán:</span>
                    <h5 class="fw-bold text-danger mb-0 mt-1">
                        {{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} VNĐ
                    </h5>
                </div>
            </div>
        </div>

        <!-- Cancellation Action -->
        @if($order->status !== 'CANCELLED')
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Hủy Đơn & Giải Phóng Ghế
                </h6>
            </div>
            <div class="card-body p-3">
                <p class="small text-secondary mb-3">
                    Thao tác này sẽ hủy đơn hàng, thu hồi vé và trả các vị trí ghế về trạng thái trống (AVAILABLE) để bán cho khán giả khác.
                </p>
                <form action="{{ url('/admin/orders/' . $order->id . '/cancel') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lý do hủy đơn</label>
                        <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Ví dụ: Khách yêu cầu hoàn tiền hoặc sai thông tin"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 btn-sm fw-bold">
                        <i class="fa-solid fa-ban me-1"></i> Xác Nhận Hủy Đơn Hàng
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
