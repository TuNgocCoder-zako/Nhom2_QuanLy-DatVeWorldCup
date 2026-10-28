@extends('layouts.app')

@section('title', 'Vé Đã Mua Của Tôi - FIFA World Cup 2030')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning text-dark fw-bold px-2 py-1 text-uppercase" style="font-size: 0.72rem;">E-Tickets</span>
                <span class="text-secondary small">Vé điện tử có mã QR định danh</span>
            </div>
            <h1 class="font-display fw-bold text-dark mb-1 fs-2">
                <i class="fa-solid fa-ticket text-warning me-2"></i> Vé Xem World Cup Của Tôi
            </h1>
            <p class="text-secondary mb-0">Quản lý vé đã đặt, tải file PDF kèm mã QR để xuất trình tại cổng soát vé sân vận động</p>
        </div>
        <div>
            <a href="{{ url('/matches') }}" class="btn btn-wc-gold btn-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Đặt thêm vé mới</span>
            </a>
        </div>
    </div>

    @if($tickets->isEmpty())
    <div class="card p-5 text-center shadow-sm border-0 bg-white rounded-4">
        <div class="wc-brand-trophy mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
            <i class="fa-solid fa-ticket"></i>
        </div>
        <h4 class="font-display fw-bold text-dark fs-3">Bạn chưa có vé nào</h4>
        <p class="text-secondary mx-auto" style="max-width: 500px;">Hãy lựa chọn các trận cầu đỉnh cao sắp diễn ra tại Tây Ban Nha, Bồ Đào Nha và Ma-rốc để sở hữu tấm vé World Cup 2030 lịch sử!</p>
        <div class="mt-3">
            <a href="{{ url('/matches') }}" class="btn btn-wc-gold px-4 shadow">
                <i class="fa-solid fa-futbol"></i>
                <span>Xem Lịch Thi Đấu & Đặt Vé</span>
            </a>
        </div>
    </div>
    @else
    <div class="row g-4">
        @foreach($tickets as $ticket)
        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm border-0 h-100 bg-white rounded-4 overflow-hidden">
                <div class="card-header p-3 d-flex justify-content-between align-items-center text-white" style="background: #0b1328 !important; border-bottom: 2px solid #f59e0b;">
                    <div>
                        <span class="badge bg-warning text-dark fw-bold me-1">Trận #{{ $ticket->match->match_number ?? $ticket->match_id }}</span>
                        <span class="small text-light">{{ $ticket->match->stage ?? 'Vòng bảng' }}</span>
                    </div>
                    <div>
                        @if($ticket->status === 'VALID')
                            <span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i>HỢP LỆ</span>
                        @elseif($ticket->status === 'CANCELLED')
                            <span class="badge bg-secondary">ĐÃ HỦY</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $ticket->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $ticket->match->home_team_flag_url }}" alt="{{ $ticket->match->home_team_name }}" class="wc-flag-shield mb-0" style="width: 38px; height: 26px;">
                            <strong class="text-dark font-display">{{ $ticket->match->home_team_name }}</strong>
                        </div>
                        <span class="badge bg-light text-dark fw-bold px-2 py-1">VS</span>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $ticket->match->away_team_flag_url }}" alt="{{ $ticket->match->away_team_name }}" class="wc-flag-shield mb-0" style="width: 38px; height: 26px;">
                            <strong class="text-dark font-display">{{ $ticket->match->away_team_name }}</strong>
                        </div>
                    </div>

                    <div class="small text-secondary mb-3">
                        <div><i class="fa-regular fa-clock me-1 text-warning"></i> {{ $ticket->match->match_time ? \Carbon\Carbon::parse($ticket->match->match_time)->format('H:i - d/m/Y') : 'Chưa định' }}</div>
                        <div><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân vận động') }}</div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Vị trí ghế:</div>
                            <strong class="text-dark fs-5 font-display">{{ $ticket->matchSeat->seat_code ?? 'N/A' }}</strong>
                            <span class="badge bg-primary ms-1">{{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }}</span>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Giá vé:</div>
                            <strong class="text-danger fs-6 font-display tnum">{{ number_format((float)$ticket->price, 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>

                    <div class="small text-muted mb-3 d-flex align-items-center justify-content-between">
                        <span>Mã vé điện tử:</span>
                        <code class="text-dark fw-bold bg-light px-2 py-1 rounded">{{ $ticket->ticket_code }}</code>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="{{ url('/my-tickets/' . $ticket->id . '/pdf') }}" class="btn btn-outline-danger btn-sm fw-bold">
                            <i class="fa-solid fa-file-pdf me-1"></i> Tải Vé PDF
                        </a>

                        @if($ticket->status === 'VALID')
                        <form action="{{ url('/my-tickets/' . $ticket->id . '/cancel') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy vé này? Ghế sẽ được giải phóng cho khán giả khác.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-ban me-1"></i> Hủy vé
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
