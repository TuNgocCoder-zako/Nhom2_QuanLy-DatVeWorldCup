@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . ($order->order_code ?? $order->id))
@section('page_title', 'Chi Tiết Đơn Hàng #' . ($order->order_code ?? $order->id))

@section('content')
<div class="mb-3">
    <a href="{{ url('/admin/orders') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách đơn hàng
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Order & Tickets -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-ticket text-warning me-2"></i> Danh Sách Vé Trong Đơn ({{ $order->tickets->count() }} vé)
                </h6>
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
            </div>
            <div class="p-0">
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
                                    <strong>{{ $ticket->match->home_team_name ?? ($ticket->match->homeTeam->name ?? 'Đội nhà') }} <span class="text-warning mx-1">vs</span> {{ $ticket->match->away_team_name ?? ($ticket->match->awayTeam->name ?? 'Đội khách') }}</strong>
                                    <div class="text-muted small">{{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân chính') }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 px-2 py-1">{{ $ticket->matchSeat->seat_code ?? 'N/A' }}</span>
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-primary border border-info border-opacity-25 ms-1 px-2 py-1">{{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }}</span>
                                </td>
                                <td class="fw-bold text-danger font-display">
                                    {{ number_format((float)$ticket->price, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    @if($ticket->status === 'VALID')
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                            HỢP LỆ
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">
                                            {{ $ticket->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ url('/my-tickets/' . $ticket->id . '/pdf') }}" target="_blank" class="btn btn-sm btn-outline-danger btn-action-icon" title="Xem vé PDF">
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
        <div class="admin-card mb-4">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user me-2 text-primary"></i> Thông Tin Khách Hàng
                </h6>
            </div>
            <div class="p-3 small">
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
                    <span class="badge rounded-pill bg-light text-secondary border px-2 py-1 ms-1">{{ $order->payment_method ?? 'VNPAY' }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Tổng thanh toán:</span>
                    <h5 class="fw-bold text-danger mb-0 mt-1 font-display">
                        {{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} VNĐ
                    </h5>
                </div>
            </div>
        </div>

        <!-- Cancellation Action -->
        @if($order->status !== 'CANCELLED')
        <div class="admin-card">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0 text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Hủy Đơn & Giải Phóng Ghế
                </h6>
            </div>
            <div class="p-3">
                <p class="small text-secondary mb-3">
                    Thao tác này sẽ hủy đơn hàng, thu hồi vé và trả các vị trí ghế về trạng thái trống (AVAILABLE) để bán cho khán giả khác.
                </p>
                <form action="{{ url('/admin/orders/' . $order->id . '/cancel') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lý do hủy đơn</label>
                        <textarea name="reason" class="form-control form-control-sm rounded-3" rows="2" placeholder="Ví dụ: Khách yêu cầu hoàn tiền hoặc sai thông tin"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 btn-sm rounded-pill fw-bold">
                        <i class="fa-solid fa-ban me-1"></i> Xác Nhận Hủy Đơn Hàng
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
