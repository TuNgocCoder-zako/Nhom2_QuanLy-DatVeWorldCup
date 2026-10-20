@extends('layouts.app')

@section('title', 'Đặt Vé Thành Công - FIFA World Cup 2030')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 text-center p-4 p-md-5 bg-white rounded-4">
                <!-- Success Icon -->
                <div class="mb-3">
                    <span class="badge bg-success rounded-circle p-3 shadow-sm" style="font-size: 2.5rem;">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>

                <h2 class="fw-bold text-dark mb-2">Thanh Toán & Đặt Vé Thành Công!</h2>
                <p class="text-secondary mb-4">
                    Cảm ơn bạn đã đặt vé xem FIFA World Cup 2030. Vé điện tử hợp lệ đã được kích hoạt và sẵn sàng để tải về.
                </p>

                <!-- Order Details Box -->
                <div class="p-3 bg-light rounded-3 text-start mb-4 border">
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            Mã đơn hàng: <strong class="text-dark">#{{ $order->order_code ?? $order->id }}</strong>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            Thời gian: <strong class="text-dark">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i - d/m/Y') }}</strong>
                        </div>
                        <div class="col-sm-6">
                            Phương thức: <strong class="text-primary">{{ $order->payment_method ?? 'VNPAY' }}</strong>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            Trạng thái đơn: <span class="badge bg-success">ĐÃ THANH TOÁN (PAID)</span>
                        </div>
                        <div class="col-12 mt-2 pt-2 border-top">
                            Tổng tiền: <strong class="text-danger fs-5">{{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>
                </div>

                <!-- Tickets List -->
                <h5 class="fw-bold text-start text-dark mb-3">
                    <i class="fa-solid fa-ticket text-warning me-2"></i> Danh Sách Vé Điện Tử ({{ $order->tickets->count() }} vé)
                </h5>

                <div class="list-group mb-4 text-start">
                    @foreach($order->tickets as $ticket)
                    <div class="list-group-item list-group-item-action p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <div class="fw-bold text-dark">
                                {{ $ticket->match->homeTeam->name ?? 'Đội nhà' }} VS {{ $ticket->match->awayTeam->name ?? 'Đội khách' }}
                            </div>
                            <small class="text-muted">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $ticket->match->match_time ? \Carbon\Carbon::parse($ticket->match->match_time)->format('H:i - d/m/Y') : '' }} &bull;
                                Sân: {{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân chính') }}
                            </small>
                            <div class="mt-1">
                                <span class="badge bg-warning text-dark me-1">Ghế: {{ $ticket->matchSeat->seat_code ?? 'A1' }}</span>
                                <span class="badge bg-info text-dark">Khu: {{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }}</span>
                                <span class="badge bg-success">Mã vé: {{ $ticket->ticket_code }}</span>
                            </div>
                        </div>

                        <div>
                            <a href="{{ url('/my-tickets/' . $ticket->id . '/pdf') }}" class="btn btn-outline-danger btn-sm fw-bold">
                                <i class="fa-solid fa-file-pdf me-1"></i> Tải Vé PDF
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ url('/my-tickets') }}" class="btn btn-warning fw-bold px-4 text-dark shadow-sm">
                        <i class="fa-solid fa-ticket me-1"></i> Quản Lý Vé Của Tôi
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-dark px-4">
                        <i class="fa-solid fa-house me-1"></i> Về Trang Chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
