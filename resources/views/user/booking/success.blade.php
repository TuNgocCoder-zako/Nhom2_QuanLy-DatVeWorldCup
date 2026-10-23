@extends('layouts.app')

@section('title', 'Đặt Vé Thành Công - FIFA World Cup 2030')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 text-center p-4 p-md-5 bg-white rounded-4" style="box-shadow: 0 16px 36px rgba(11, 19, 40, 0.08) !important;">
                <!-- Success Icon -->
                <div class="mb-3">
                    <span class="badge bg-success rounded-circle p-3 shadow-sm" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem;">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>

                <div class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 text-uppercase mx-auto mb-2" style="font-size: 0.75rem;">
                    Giao Dịch Thành Công
                </div>

                <h1 class="font-display fw-bold text-dark fs-2 mb-2">Thanh Toán & Đặt Vé Thành Công!</h1>
                <p class="text-secondary mb-4 mx-auto" style="max-width: 580px;">
                    Cảm ơn bạn đã đặt vé xem FIFA World Cup 2030. Vé điện tử hợp lệ đã được kích hoạt, mã QR bảo mật đã sẵn sàng để tải về và lưu vào thiết bị của bạn.
                </p>

                <!-- Order Details Box -->
                <div class="p-3 bg-light rounded-3 text-start mb-4 border">
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            Mã đơn hàng: <strong class="text-dark font-display">#{{ $order->order_code ?? $order->id }}</strong>
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
                        <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Tổng tiền thanh toán:</span>
                            <strong class="text-danger fs-5 font-display tnum">{{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>
                </div>

                <!-- Tickets List -->
                <h5 class="fw-bold text-start text-dark mb-3 font-display">
                    <i class="fa-solid fa-ticket text-warning me-2"></i> Danh Sách Vé Điện Tử ({{ $order->tickets->count() }} vé)
                </h5>

                <div class="list-group mb-4 text-start">
                    @foreach($order->tickets as $ticket)
                    <div class="list-group-item list-group-item-action p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 rounded-3 mb-2 border">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <img src="{{ $ticket->match->homeTeam->flag_url }}" alt="" class="wc-flag-shield mb-0" style="width: 28px; height: 18px;">
                                <strong class="text-dark">{{ $ticket->match->homeTeam->name ?? 'Đội nhà' }}</strong>
                                <span class="badge bg-light text-secondary">VS</span>
                                <img src="{{ $ticket->match->awayTeam->flag_url }}" alt="" class="wc-flag-shield mb-0" style="width: 28px; height: 18px;">
                                <strong class="text-dark">{{ $ticket->match->awayTeam->name ?? 'Đội khách' }}</strong>
                            </div>
                            <small class="text-muted d-block">
                                <i class="fa-regular fa-calendar me-1"></i> {{ $ticket->match->match_time ? \Carbon\Carbon::parse($ticket->match->match_time)->format('H:i - d/m/Y') : '' }} &bull;
                                Sân: {{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân chính') }}
                            </small>
                            <div class="mt-1">
                                <span class="badge bg-warning text-dark me-1">Ghế: {{ $ticket->matchSeat->seat_code ?? 'A1' }}</span>
                                <span class="badge bg-secondary">{{ $ticket->ticketType->name ?? 'STANDARD' }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-row flex-md-column gap-2 text-md-end align-items-center align-items-md-end">
                            <span class="fw-bold text-danger fs-6 tnum">{{ number_format((float)$ticket->price, 0, ',', '.') }} VNĐ</span>
                            <a href="{{ url('/my-tickets/' . $ticket->id . '/pdf') }}" class="btn btn-outline-danger btn-sm fw-bold">
                                <i class="fa-solid fa-file-pdf me-1"></i> Tải PDF QR
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Navigation buttons -->
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ url('/my-tickets') }}" class="btn btn-wc-gold px-4 shadow">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Xem Tất Cả Vé Của Tôi</span>
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
