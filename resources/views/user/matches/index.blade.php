@extends('layouts.app')

@section('title', 'Lịch Thi Đấu & Đặt Vé - FIFA World Cup 2030')

@section('content')
<div class="container py-4">
    <!-- Header Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-calendar-check text-warning me-2"></i> Lịch Thi Đấu FIFA World Cup 2030
            </h2>
            <p class="text-secondary mb-0">Chọn trận đấu yêu thích và lựa chọn ghế ngồi trực quan trên sơ đồ sân vận động</p>
        </div>
        <div>
            <span class="badge bg-dark px-3 py-2 fs-6">
                Tổng cộng: <strong class="text-warning">{{ $matches->total() }}</strong> trận đấu
            </span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ url('/matches') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-dark"><i class="fa-regular fa-calendar me-1"></i> Ngày thi đấu</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-dark"><i class="fa-solid fa-flag me-1"></i> Đội tuyển</label>
                    <select name="team" class="form-select form-select-sm">
                        <option value="">-- Tất cả đội tuyển --</option>
                        @foreach($teams as $t)
                            <option value="{{ $t->id }}" {{ request('team') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-sm-6">
                    <label class="form-label small fw-bold text-dark"><i class="fa-solid fa-building me-1"></i> Sân vận động</label>
                    <select name="stadium" class="form-select form-select-sm">
                        <option value="">-- Tất cả sân vận động --</option>
                        @foreach($stadiums as $s)
                            <option value="{{ $s->id }}" {{ request('stadium') == $s->id ? 'selected' : '' }}>
                                {{ $s->stadium_name ?? $s->name }} ({{ $s->location ?? 'Tây Ban Nha' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-warning btn-sm fw-bold w-100 text-dark">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    @if(request()->hasAny(['date', 'team', 'stadium']))
                        <a href="{{ url('/matches') }}" class="btn btn-outline-secondary btn-sm" title="Xóa bộ lọc">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Match List Grid -->
    <div class="row g-4">
        @forelse($matches as $match)
        <div class="col-lg-6 col-md-12">
            <div class="card match-card shadow-sm h-100 border">
                <div class="match-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-warning text-dark fw-bold">Trận #{{ $match->match_number ?? $match->id }}</span>
                    <span><i class="fa-solid fa-trophy text-warning me-1"></i> {{ $match->stage ?? 'Vòng bảng' }}</span>
                    <span class="text-light">
                        <i class="fa-regular fa-clock me-1"></i>
                        {{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định ngày' }}
                    </span>
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

                        <!-- VS Badge & Tỷ số nếu có -->
                        <div class="col-2">
                            @if($match->status === 'FINISHED' || $match->status === 'LIVE')
                                <div class="fs-4 fw-bold text-danger">
                                    {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                </div>
                                <span class="badge {{ $match->status === 'LIVE' ? 'bg-danger' : 'bg-secondary' }} mt-1">
                                    {{ $match->status }}
                                </span>
                            @else
                                <span class="vs-badge">VS</span>
                            @endif
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
                        <small class="text-muted text-truncate" style="max-width: 65%;">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            {{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}
                            ({{ $match->stadium->location ?? 'Tây Ban Nha' }})
                        </small>
                        <a href="{{ url('/matches/' . $match->id) }}" class="btn btn-warning btn-sm fw-bold px-3 text-dark">
                            <i class="fa-solid fa-chair me-1"></i> Sơ đồ & Chọn Ghế
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card p-5 text-center shadow-sm border-0">
                <i class="fa-solid fa-magnifying-glass fa-3x text-muted mb-3"></i>
                <h4 class="fw-bold text-dark">Không tìm thấy trận đấu phù hợp</h4>
                <p class="text-secondary">Hãy thử thay đổi tiêu chí tìm kiếm hoặc xóa bộ lọc.</p>
                <div class="mt-2">
                    <a href="{{ url('/matches') }}" class="btn btn-outline-dark btn-sm">Xem tất cả trận đấu</a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $matches->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
