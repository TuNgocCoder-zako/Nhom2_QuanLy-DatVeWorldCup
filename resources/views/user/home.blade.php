@extends('layouts.app')

@section('title', 'FIFA World Cup 2030 - Trang Chủ Đặt Vé Trực Tuyến')

@section('content')
<!-- Hero Banner -->
<section class="hero-section text-center">
    <div class="container">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">
            <i class="fa-solid fa-calendar-days me-1"></i> 100 Năm Lịch Sử World Cup (1930 - 2030)
        </span>
        <h1 class="hero-title mb-3">FIFA WORLD CUP 2030</h1>
        <p class="lead text-light opacity-75 max-w-700 mx-auto mb-4" style="max-width: 650px;">
            Chào mừng bạn đến với cổng bán vé chính thức giải vô địch bóng đá thế giới FIFA World Cup 2030 tổ chức tại Tây Ban Nha, Bồ Đào Nha và Ma-rốc.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/matches') }}" class="btn btn-warning btn-lg px-4 fw-bold text-dark shadow">
                <i class="fa-solid fa-ticket me-2"></i> Đặt Vé Ngay
            </a>
            <a href="#groups-section" class="btn btn-outline-light btn-lg px-4">
                <i class="fa-solid fa-list-ol me-2"></i> Xem Bảng Đấu
            </a>
        </div>
    </div>
</section>

<!-- Hot Matches Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-warning fw-bold text-uppercase mb-1"><i class="fa-solid fa-fire me-1"></i> Tâm Điểm</h6>
                <h2 class="fw-bold text-dark mb-0">Các Trận Cầu Đỉnh Cao Sắp Diễn Ra</h2>
            </div>
            <a href="{{ url('/matches') }}" class="btn btn-outline-dark btn-sm fw-semibold">
                Xem tất cả lịch thi đấu <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($hotMatches as $match)
            <div class="col-lg-6 col-md-12">
                <div class="card match-card shadow-sm h-100 border">
                    <div class="match-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-warning text-dark fw-bold">Trận #{{ $match->match_number ?? $match->id }}</span>
                        <span><i class="fa-solid fa-trophy text-warning me-1"></i> {{ $match->stage ?? 'Vòng bảng' }}</span>
                        <span class="text-light"><i class="fa-regular fa-clock me-1"></i> {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định ngày' }}</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row align-items-center text-center">
                            <!-- Đội nhà -->
                            <div class="col-5">
                                @if($match->homeTeam && $match->homeTeam->flag_url)
                                    <img src="{{ $match->homeTeam->flag_url }}" alt="{{ $match->homeTeam->name }}" class="team-flag mb-2 shadow-sm">
                                @else
                                    <div class="badge bg-secondary p-2 mb-2"><i class="fa-solid fa-flag"></i></div>
                                @endif
                                <div class="team-name text-dark">{{ $match->homeTeam->name ?? 'Đội nhà' }}</div>
                                <span class="badge bg-light text-secondary border">{{ $match->homeTeam->code ?? 'HOM' }}</span>
                            </div>

                            <!-- VS Badge -->
                            <div class="col-2">
                                <span class="vs-badge">VS</span>
                            </div>

                            <!-- Đội khách -->
                            <div class="col-5">
                                @if($match->awayTeam && $match->awayTeam->flag_url)
                                    <img src="{{ $match->awayTeam->flag_url }}" alt="{{ $match->awayTeam->name }}" class="team-flag mb-2 shadow-sm">
                                @else
                                    <div class="badge bg-secondary p-2 mb-2"><i class="fa-solid fa-flag"></i></div>
                                @endif
                                <div class="team-name text-dark">{{ $match->awayTeam->name ?? 'Đội khách' }}</div>
                                <span class="badge bg-light text-secondary border">{{ $match->awayTeam->code ?? 'AWA' }}</span>
                            </div>
                        </div>

                        <hr class="my-3 opacity-25">

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                {{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}
                                ({{ $match->stadium->location ?? 'Tây Ban Nha' }})
                            </small>
                            <a href="{{ url('/matches/' . $match->id) }}" class="btn btn-warning btn-sm fw-bold px-3 text-dark">
                                <i class="fa-solid fa-chair me-1"></i> Chọn Ghế Ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">Đang cập nhật danh sách các trận đấu sắp diễn ra.</div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Groups Standings Section -->
<section class="py-5 bg-light" id="groups-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary text-uppercase px-3 py-2 fw-semibold">48 Đội Tuyển - 12 Bảng Đấu</span>
            <h2 class="fw-bold mt-2 text-dark">Các Bảng Đấu FIFA World Cup 2030</h2>
            <p class="text-secondary">Khám phá danh sách các đội tuyển tranh tài tại 3 quốc gia chủ nhà</p>
        </div>

        <div class="row g-4">
            @foreach($teamsByGroup as $groupName => $teams)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="group-header d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-shield me-1"></i> Bảng {{ $groupName }}</span>
                        <span class="badge bg-warning text-dark">{{ count($teams) }} Đội</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($teams as $team)
                        <li class="list-group-item d-flex align-items-center justify-content-between py-2">
                            <div class="d-flex align-items-center gap-2">
                                @if($team->flag_url)
                                    <img src="{{ $team->flag_url }}" alt="{{ $team->name }}" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px;">
                                @endif
                                <span class="fw-semibold small text-dark">{{ $team->name }}</span>
                            </div>
                            <span class="badge bg-light text-secondary border small">{{ $team->code }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Stadiums -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-warning fw-bold text-uppercase"><i class="fa-solid fa-landmark me-1"></i> Thánh Địa Bóng Đá</h6>
            <h2 class="fw-bold text-dark">Các Sân Vận Động Đăng Cai</h2>
        </div>

        <div class="row g-4">
            @foreach($featuredStadiums as $stadium)
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center p-3">
                    <div class="rounded p-3 bg-light mb-3 text-warning">
                        <i class="fa-solid fa-building-columns fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $stadium->stadium_name ?? $stadium->name }}</h5>
                    <p class="text-muted small mb-2"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $stadium->location ?? 'Tây Ban Nha' }}</p>
                    <div class="mt-auto">
                        <span class="badge bg-secondary">Sức chứa: {{ number_format((int)$stadium->capacity) }} chỗ</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
