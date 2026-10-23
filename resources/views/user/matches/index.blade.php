@extends('layouts.app')

@section('title', 'Lịch Thi Đấu & Đặt Vé - FIFA World Cup 2030')

@section('content')
<div class="container py-4">
    <!-- Header Title & Tournament Badge -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark fw-bold px-2 py-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                    <i class="fa-solid fa-trophy me-1"></i> Lịch Thi Đấu Chính Thức
                </span>
                <span class="text-secondary small">&bull; 48 Đội Tuyển &bull; 104 Trận Đấu</span>
            </div>
            <h1 class="font-display fw-bold text-dark mb-1 fs-2">
                Lịch Thi Đấu & Đặt Vé FIFA World Cup 2030
            </h1>
            <p class="text-secondary mb-0">Lựa chọn trận cầu đỉnh cao và chọn vị trí ghế ngồi trực quan trên sơ đồ sân vận động</p>
        </div>
        <div>
            <div class="card bg-dark text-white border-0 shadow-sm px-3 py-2 text-center" style="background: #0b1328 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 12px;">
                <small class="text-secondary" style="font-size: 0.75rem;">Kết quả tìm kiếm</small>
                <div class="fs-5 fw-bold text-warning font-display">{{ $matches->total() }} <span class="fs-6 text-light fw-normal">Trận đấu</span></div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="wc-filter-toolbar">
        <form action="{{ url('/matches') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-dark"><i class="fa-regular fa-calendar me-1 text-warning"></i> Ngày thi đấu</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-dark"><i class="fa-solid fa-flag me-1 text-warning"></i> Đội tuyển</label>
                <select name="team" class="form-select form-select-sm">
                    <option value="">-- Tất cả đội tuyển --</option>
                    @foreach($teams as $t)
                        <option value="{{ $t->id }}" {{ request('team') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-4 col-md-8">
                <label class="form-label small fw-bold text-dark"><i class="fa-solid fa-landmark me-1 text-warning"></i> Sân vận động</label>
                <select name="stadium" class="form-select form-select-sm">
                    <option value="">-- Tất cả sân vận động --</option>
                    @foreach($stadiums as $s)
                        <option value="{{ $s->id }}" {{ request('stadium') == $s->id ? 'selected' : '' }}>
                            {{ $s->stadium_name ?? $s->name }} ({{ $s->location }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-wc-gold btn-sm w-100">
                    <i class="fa-solid fa-filter"></i>
                    <span>Lọc</span>
                </button>
                @if(request()->hasAny(['date', 'team', 'stadium']))
                    <a href="{{ url('/matches') }}" class="btn btn-outline-secondary btn-sm" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Match Cards Grid -->
    <div class="row g-4">
        @forelse($matches as $match)
        <div class="col-lg-6 col-md-12">
            <div class="wc-match-card h-100">
                <!-- Card Header -->
                <div class="wc-match-header">
                    <span class="wc-match-badge-stage">
                        <i class="fa-solid fa-trophy me-1"></i> {{ $match->stage ?? 'Vòng bảng' }} &bull; Trận #{{ $match->match_number ?? $match->id }}
                    </span>
                    <div class="wc-match-time-tag">
                        <i class="fa-regular fa-calendar-check text-warning"></i>
                        <span>{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định ngày' }}</span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="wc-match-body">
                    <div class="row align-items-center text-center">
                        <!-- Đội nhà -->
                        <div class="col-5 wc-team-col">
                            <img src="{{ $match->homeTeam->flag_url }}" alt="{{ $match->homeTeam->name }}" class="wc-flag-shield" loading="lazy">
                            <div class="wc-team-title">{{ $match->homeTeam->name ?? 'Đội nhà' }}</div>
                            <span class="wc-code-pill">{{ $match->homeTeam->code ?? 'HOM' }}</span>
                        </div>

                        <!-- VS / Tỷ số -->
                        <div class="col-2 wc-vs-box">
                            @if($match->status === 'FINISHED' || $match->status === 'LIVE')
                                <div class="wc-score-badge">
                                    {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                </div>
                                <span class="badge {{ $match->status === 'LIVE' ? 'bg-danger' : 'bg-secondary' }} small">
                                    {{ $match->status }}
                                </span>
                            @else
                                <span class="wc-vs-badge">VS</span>
                            @endif
                        </div>

                        <!-- Đội khách -->
                        <div class="col-5 wc-team-col">
                            <img src="{{ $match->awayTeam->flag_url }}" alt="{{ $match->awayTeam->name }}" class="wc-flag-shield" loading="lazy">
                            <div class="wc-team-title">{{ $match->awayTeam->name ?? 'Đội khách' }}</div>
                            <span class="wc-code-pill">{{ $match->awayTeam->code ?? 'AWA' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="wc-match-footer">
                    <div class="wc-match-venue">
                        <i class="fa-solid fa-location-dot text-danger"></i>
                        <span class="fw-medium text-dark text-truncate" style="max-width: 220px;" title="{{ $match->stadium->stadium_name ?? $match->stadium->name }}">
                            {{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="wc-seats-avail">
                            <i class="fa-solid fa-chair"></i>
                            <span>{{ $match->available_seats_count }} chỗ</span>
                        </span>
                        <a href="{{ url('/matches/' . $match->id) }}" class="btn btn-wc-gold btn-sm px-3">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Chọn Ghế</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card p-5 text-center shadow-sm border-0 rounded-4">
                <i class="fa-solid fa-magnifying-glass fa-3x text-muted mb-3"></i>
                <h4 class="fw-bold text-dark font-display">Không tìm thấy trận đấu phù hợp</h4>
                <p class="text-secondary">Hãy thử điều chỉnh lại bộ lọc theo ngày, đội tuyển hoặc sân vận động.</p>
                <div class="mt-2">
                    <a href="{{ url('/matches') }}" class="btn btn-outline-dark btn-sm px-4">
                        <i class="fa-solid fa-rotate-left me-1"></i> Xem tất cả trận đấu
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-5 d-flex justify-content-center">
        {{ $matches->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
