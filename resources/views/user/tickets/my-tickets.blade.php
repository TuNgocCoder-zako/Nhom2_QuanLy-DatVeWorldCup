@extends('layouts.app')

@section('title', 'Vé Đã Mua Của Tôi - FIFA World Cup 2030')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-ticket text-warning me-2"></i> Vé Của Tôi
            </h2>
            <p class="text-secondary mb-0">Quản lý vé xem World Cup 2030, tải file PDF hoặc thực hiện hủy vé khi cần</p>
        </div>
        <div>
            <a href="{{ url('/matches') }}" class="btn btn-warning btn-sm fw-bold text-dark">
                <i class="fa-solid fa-plus me-1"></i> Đặt thêm vé mới
            </a>
        </div>
    </div>

    @if($tickets->isEmpty())
    <div class="card p-5 text-center shadow-sm border-0 bg-white">
        <i class="fa-solid fa-ticket fa-3x text-muted mb-3"></i>
        <h4 class="fw-bold text-dark">Bạn chưa có vé nào</h4>
        <p class="text-secondary">Hãy lựa chọn các trận cầu đỉnh cao và chọn cho mình vị trí ngồi đẹp nhất!</p>
        <div class="mt-2">
            <a href="{{ url('/matches') }}" class="btn btn-warning fw-bold text-dark px-4">
                <i class="fa-solid fa-futbol me-1"></i> Xem Lịch Thi Đấu & Đặt Vé
            </a>
        </div>
    </div>
    @else
    <div class="row g-4">
        @foreach($tickets as $ticket)
        <div class="col-lg-6 col-md-12">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center" style="background: #0b132b !important;">
                    <div>
                        <span class="badge bg-warning text-dark fw-bold me-1">Trận #{{ $ticket->match->match_number ?? $ticket->match_id }}</span>
                        <span class="small text-light">{{ $ticket->match->stage ?? 'Vòng bảng' }}</span>
                    </div>
                    <div>
                        @if($ticket->status === 'VALID')
                            <span class="badge bg-success">HỢP LỆ</span>
                        @elseif($ticket->status === 'CANCELLED')
                            <span class="badge bg-secondary">ĐÃ HỦY</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $ticket->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-2">
                        {{ $ticket->match->homeTeam->name ?? 'Đội nhà' }} <span class="text-warning">VS</span> {{ $ticket->match->awayTeam->name ?? 'Đội khách' }}
                    </h5>

                    <div class="small text-secondary mb-3">
                        <div><i class="fa-regular fa-clock me-1 text-warning"></i> {{ $ticket->match->match_time ? \Carbon\Carbon::parse($ticket->match->match_time)->format('H:i - d/m/Y') : 'Chưa định' }}</div>
                        <div><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $ticket->match->stadium->stadium_name ?? ($ticket->match->stadium->name ?? 'Sân vận động') }}</div>
                    </div>

                    <div class="p-3 bg-light rounded mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Vị trí ghế:</div>
                            <strong class="text-dark fs-5">{{ $ticket->matchSeat->seat_code ?? 'N/A' }}</strong>
                            <span class="badge bg-info text-dark ms-1">{{ $ticket->matchSeat->seatZone->name ?? 'STANDARD' }}</span>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Giá vé:</div>
                            <strong class="text-danger fs-6">{{ number_format((float)$ticket->price, 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>

                    <div class="small text-muted mb-3">
                        Mã vé điện tử: <code class="text-dark fw-bold">{{ $ticket->ticket_code }}</code>
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
