@extends('layouts.admin')

@section('title', 'Cập Nhật Trận Đấu - FIFA World Cup 2030')
@section('page_title', 'Cập Nhật Trận Đấu #' . ($match->match_number ?? $match->id))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Chỉnh Sửa Trận Đấu #{{ $match->match_number ?? $match->id }}</h6>
                <a href="{{ url('/admin/matches') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('/admin/matches/' . $match->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <strong>{{ $match->homeTeam->name ?? 'Team A' }}</strong> VS <strong>{{ $match->awayTeam->name ?? 'Team B' }}</strong>
                        <div class="text-muted small">Sân: {{ $match->stadium->stadium_name ?? $match->stadium->name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Thời gian thi đấu</label>
                        <input type="datetime-local" name="match_time" class="form-control" value="{{ $match->match_time ? date('Y-m-d\TH:i', strtotime($match->match_time)) : '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Trạng thái trận đấu</label>
                        <select name="status" class="form-select" required>
                            <option value="UPCOMING" {{ $match->status === 'UPCOMING' ? 'selected' : '' }}>Sắp diễn ra (UPCOMING)</option>
                            <option value="LIVE" {{ $match->status === 'LIVE' ? 'selected' : '' }}>Đang diễn ra (LIVE)</option>
                            <option value="FINISHED" {{ $match->status === 'FINISHED' ? 'selected' : '' }}>Đã kết thúc (FINISHED)</option>
                            <option value="CANCELLED" {{ $match->status === 'CANCELLED' ? 'selected' : '' }}>Hủy bỏ (CANCELLED)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bàn thắng {{ $match->homeTeam->name ?? 'Home' }}</label>
                            <input type="number" name="home_score" class="form-control" value="{{ $match->home_score ?? 0 }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bàn thắng {{ $match->awayTeam->name ?? 'Away' }}</label>
                            <input type="number" name="away_score" class="form-control" value="{{ $match->away_score ?? 0 }}" min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/matches') }}" class="btn btn-secondary btn-sm">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fa-solid fa-check me-1"></i> Cập Nhật Trận Đấu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
