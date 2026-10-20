@extends('layouts.app')

@section('title', 'Sơ Đồ Chọn Ghế: ' . ($match->homeTeam->name ?? 'Home') . ' vs ' . ($match->awayTeam->name ?? 'Away'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/matches') }}" class="text-decoration-none">Lịch thi đấu</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chọn ghế trận #{{ $match->match_number ?? $match->id }}</li>
        </ol>
    </nav>

    <!-- Match Header Banner -->
    <div class="card shadow-sm border-0 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%); border-bottom: 3px solid #ffb703;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold">Trận #{{ $match->match_number ?? $match->id }}</span>
                        <span class="badge bg-secondary">{{ $match->stage ?? 'Vòng bảng' }}</span>
                        <span class="text-warning"><i class="fa-regular fa-clock me-1"></i> {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định ngày' }}</span>
                    </div>

                    <h2 class="fw-bold mb-2 text-white d-flex align-items-center gap-3 flex-wrap">
                        <span>{{ $match->homeTeam->name ?? 'Đội nhà' }}</span>
                        <span class="text-warning fs-4">VS</span>
                        <span>{{ $match->awayTeam->name ?? 'Đội khách' }}</span>
                    </h2>

                    <div class="text-light opacity-75 small">
                        <i class="fa-solid fa-location-dot text-danger me-1"></i>
                        <strong>{{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}</strong>
                        &bull; {{ $match->stadium->location ?? 'Tây Ban Nha' }}
                        &bull; Sức chứa: {{ number_format((int)($match->stadium->capacity ?? 80000)) }} chỗ
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ url('/matches') }}" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Chọn trận đấu khác
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Interactive Seat Map Column -->
        <div class="col-lg-8">
            <div class="stadium-container" id="seatMapContainer" data-match-id="{{ $match->id }}">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 pb-2 border-bottom border-secondary border-opacity-25 gap-2">
                    <h5 class="fw-bold mb-0 text-warning">
                        <i class="fa-solid fa-map-location-dot me-2"></i> SƠ ĐỒ CHỖ NGỒI
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
                <div class="pitch-container">
                    <div class="pitch-line-center"></div>
                    <div class="pitch-circle-center"></div>
                    <div class="pitch-label">
                        <i class="fa-solid fa-futbol me-2"></i> SÂN THI ĐẤU (PITCH)
                    </div>
                </div>

                <!-- Loading State -->
                <div id="seatMapLoading" class="text-center py-5">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-secondary mt-2 mb-0">Đang tải dữ liệu sơ đồ ghế ngồi trực quan...</p>
                </div>

                <!-- Rendered Grandstands / Zones -->
                <div id="zonesContainer"></div>
            </div>
        </div>

        <!-- Booking Cart Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card booking-summary-card shadow-sm sticky-top" style="top: 85px;">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center" style="background: #0b132b !important;">
                    <span class="fw-bold text-warning"><i class="fa-solid fa-cart-shopping me-2"></i> Giỏ Vé Của Bạn</span>
                    <span class="badge bg-warning text-dark fw-bold px-2 py-1" id="selectedCount">0</span>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-2">Ghế đang chọn:</h6>
                    <div id="selectedSeatsList" class="p-3 bg-light rounded mb-3 min-vh-20" style="min-height: 85px;">
                        <span class="text-muted small">Chưa có ghế nào được chọn. Hãy nhấp vào các ô ghế màu xanh trên sơ đồ.</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-3">
                        <span class="fw-bold text-dark">Tổng tạm tính:</span>
                        <span class="fw-bold fs-5 text-danger" id="totalAmount">0 VNĐ</span>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Tối đa <strong>8 ghế</strong> / đơn hàng. Ghế sẽ được giữ chỗ an toàn ngay khi chuyển sang thanh toán.
                    </div>

                    <!-- Booking Form posting to /booking/checkout -->
                    <form action="{{ url('/booking/checkout') }}" method="POST" id="bookingForm">
                        @csrf
                        <input type="hidden" name="match_id" value="{{ $match->id }}">
                        <div id="hiddenSeatIds"></div>

                        <button type="submit" id="btnProceedBooking" class="btn btn-warning w-100 fw-bold py-2 text-dark shadow-sm" disabled>
                            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Tiến Hành Đặt Vé
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/seat-map.js') }}"></script>
@endsection
