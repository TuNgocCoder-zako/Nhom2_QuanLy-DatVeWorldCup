@extends('layouts.app')

@section('title', 'Sơ Đồ Chọn Ghế: ' . ($match->homeTeam->name ?? 'Home') . ' vs ' . ($match->awayTeam->name ?? 'Away') . ' - FIFA World Cup 2030')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/seat-map.css') }}?v={{ filemtime(public_path('css/seat-map.css')) }}">
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-secondary">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/matches') }}" class="text-decoration-none text-secondary">Lịch thi đấu</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Chọn ghế trận #{{ $match->match_number ?? $match->id }}</li>
        </ol>
    </nav>

    <!-- Match Header Banner (Broadcast Theme) -->
    <div class="card shadow-sm border-0 mb-4 text-white overflow-hidden rounded-4" style="background: radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.15) 0%, transparent 50%), linear-gradient(135deg, #060a17 0%, #0b1328 100%); border-bottom: 3px solid #f59e0b;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1 text-uppercase" style="font-size: 0.75rem;">
                            Trận #{{ $match->match_number ?? $match->id }}
                        </span>
                        <span class="badge bg-dark border border-secondary border-opacity-50 text-light px-2 py-1">
                            <i class="fa-solid fa-trophy text-warning me-1"></i> {{ $match->stage ?? 'Vòng bảng' }}
                        </span>
                        <span class="text-warning small fw-bold">
                            <i class="fa-regular fa-clock me-1"></i> {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định ngày' }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $match->home_team_flag_url }}" alt="{{ $match->home_team_name }}" class="wc-flag-shield mb-0" style="width: 48px; height: 32px;" loading="lazy">
                            <h2 class="font-display fw-bold text-white mb-0 fs-3">{{ $match->home_team_name }}</h2>
                        </div>
                        <span class="badge bg-light text-dark fw-bold px-3 py-1 fs-6">VS</span>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $match->away_team_flag_url }}" alt="{{ $match->away_team_name }}" class="wc-flag-shield mb-0" style="width: 48px; height: 32px;" loading="lazy">
                            <h2 class="font-display fw-bold text-white mb-0 fs-3">{{ $match->away_team_name }}</h2>
                        </div>
                    </div>

                    <div class="text-light opacity-75 small">
                        <i class="fa-solid fa-location-dot text-danger me-1"></i>
                        <strong>{{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}</strong>
                        &bull; {{ $match->stadium->location ?? 'Tây Ban Nha' }}
                        &bull; Sức chứa: <strong>{{ number_format((int)($match->stadium->capacity ?? 80000)) }}</strong> chỗ
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ url('/matches') }}" class="btn btn-wc-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Xem trận khác</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Interactive Seat Map Column -->
        <div class="col-lg-8">
            <div class="stadium-container" id="seatMapContainer" 
                 data-match-id="{{ $match->id }}"
                 data-seats-url="{{ url('/api/matches/' . $match->id . '/seats') }}"
                 data-check-seats-url="{{ url('/api/matches/' . $match->id . '/check-seats') }}">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 pb-3 border-bottom border-secondary border-opacity-25 gap-2">
                    <h5 class="fw-bold mb-0 text-warning font-display">
                        <i class="fa-solid fa-map-location-dot me-2"></i> SƠ ĐỒ CHỖ NGỒI SÂN VẬN ĐỘNG
                    </h5>

                    <!-- Legend -->
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <div class="legend-item">
                            <span class="legend-box available"></span>
                            <small class="text-white">Ghế trống</small>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box selected"></span>
                            <small class="text-warning fw-bold">Đang chọn</small>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box booked"></span>
                            <small class="text-secondary">Đã đặt</small>
                        </div>
                    </div>
                </div>

                <!-- Football Pitch Simulation -->
                <div class="pitch-container" aria-hidden="true">
                    <div class="pitch-line-center"></div>
                    <div class="pitch-circle-center"></div>
                    <div class="pitch-label">
                        <i class="fa-solid fa-futbol text-warning"></i> SÂN THI ĐẤU (PITCH)
                    </div>
                </div>

                <!-- Loading State -->
                <div id="seatMapLoading" class="text-center py-5">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-secondary mt-2 mb-0">Đang tải sơ đồ ghế ngồi trực quan theo từng khán đài...</p>
                </div>

                <!-- Rendered Grandstands / Zones -->
                <div id="zonesContainer"></div>
            </div>
        </div>

        <!-- Booking Cart Summary Sidebar -->
        <div class="col-lg-4">
            <div class="booking-summary-card sticky-top" style="top: 85px;">
                <div class="booking-summary-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-warning font-display">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Giỏ Vé Của Bạn
                    </span>
                    <span class="badge bg-warning text-dark fw-bold px-2 py-1" id="selectedCount">0</span>
                </div>

                <div class="p-4">
                    <h6 class="fw-bold text-dark mb-2">Vị trí ghế đã chọn:</h6>
                    <div id="selectedSeatsList" class="p-3 bg-light rounded-3 mb-3" style="min-height: 85px;">
                        <span class="text-muted small">Chưa có ghế nào được chọn. Nhấp vào các ô ghế màu xanh trên sơ đồ sân vận động.</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-3">
                        <span class="fw-bold text-dark">Tổng tiền tạm tính:</span>
                        <span class="fw-bold fs-5 text-danger font-display tnum" id="totalAmount">0 VNĐ</span>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-3 border-0" style="background: #eff6ff; color: #1e40af;">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Tối đa <strong>8 vé</strong> / đơn đặt. Vé sẽ được giữ chỗ an toàn ngay khi bấm tiến hành đặt vé.
                    </div>

                    <!-- Booking Form posting to /booking/checkout -->
                    <form action="{{ url('/booking/checkout') }}" method="POST" id="bookingForm">
                        @csrf
                        <input type="hidden" name="match_id" value="{{ $match->id }}">
                        <div id="hiddenSeatIds"></div>

                        <button type="submit" id="btnProceedBooking" class="btn btn-wc-gold w-100 py-3 shadow" disabled>
                            <i class="fa-solid fa-arrow-right"></i>
                            <span>Tiến Hành Đặt Vé</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Squad Section (Danh sách cầu thủ 2 đội) -->
    @if(($match->homeTeam && $match->homeTeam->players->isNotEmpty()) || ($match->awayTeam && $match->awayTeam->players->isNotEmpty()))
    <div class="row g-4 mt-3">
        <div class="col-12">
            <h4 class="font-display fw-bold text-dark mb-0">
                <i class="fa-solid fa-users text-warning me-2"></i> Danh Sách Cầu Thủ Đăng Ký Thi Đấu
            </h4>
            <p class="text-muted small mb-0">Đội hình chính thức và số áo của 2 đội tuyển tham dự trận đấu này.</p>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $match->home_team_flag_url }}" alt="{{ $match->home_team_name }}" style="width: 28px; height: 18px; border-radius: 3px; object-fit: cover;">
                        <span class="fw-bold text-dark font-display">{{ $match->home_team_name }}</span>
                    </div>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                        {{ $match->homeTeam ? $match->homeTeam->players->count() : 0 }} cầu thủ
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-3" style="width: 60px;">Số</th>
                                    <th>Họ và tên</th>
                                    <th class="text-end pe-3">Vị trí</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($match->homeTeam->players ?? [] as $p)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">#{{ $p->shirt_number ?? '-' }}</td>
                                    <td class="fw-bold text-dark">{{ $p->name }}</td>
                                    <td class="text-end pe-3">
                                        <span class="badge rounded-pill {{ $p->position === 'GK' ? 'bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50' : ($p->position === 'FW' ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : ($p->position === 'MF' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-info bg-opacity-10 text-primary border border-info border-opacity-25')) }} px-2 py-1">
                                            {{ $p->position_text }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">Chưa có danh sách cầu thủ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $match->away_team_flag_url }}" alt="{{ $match->away_team_name }}" style="width: 28px; height: 18px; border-radius: 3px; object-fit: cover;">
                        <span class="fw-bold text-dark font-display">{{ $match->away_team_name }}</span>
                    </div>
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                        {{ $match->awayTeam ? $match->awayTeam->players->count() : 0 }} cầu thủ
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-3" style="width: 60px;">Số</th>
                                    <th>Họ và tên</th>
                                    <th class="text-end pe-3">Vị trí</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($match->awayTeam->players ?? [] as $p)
                                <tr>
                                    <td class="ps-3 fw-bold text-danger">#{{ $p->shirt_number ?? '-' }}</td>
                                    <td class="fw-bold text-dark">{{ $p->name }}</td>
                                    <td class="text-end pe-3">
                                        <span class="badge rounded-pill {{ $p->position === 'GK' ? 'bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50' : ($p->position === 'FW' ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : ($p->position === 'MF' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-info bg-opacity-10 text-primary border border-info border-opacity-25')) }} px-2 py-1">
                                            {{ $p->position_text }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">Chưa có danh sách cầu thủ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/seat-map.js') }}?v={{ filemtime(public_path('js/seat-map.js')) }}"></script>
@endsection
