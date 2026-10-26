@extends('layouts.admin')

@section('title', 'Cập Nhật Trận Đấu - FIFA World Cup 2030')
@section('page_title', 'Cập Nhật Trận Đấu #' . ($match->match_number ?? $match->id))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Chỉnh Sửa Trận Đấu #{{ $match->match_number ?? $match->id }}</h6>
                <a href="{{ url('/admin/matches') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="p-4">
                <form action="{{ url('/admin/matches/' . $match->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-3 bg-light rounded-4 mb-4 border d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark font-display fs-5">{{ $match->home_team_name }}</span>
                            <span class="text-warning mx-2 fw-bold">VS</span>
                            <span class="fw-bold text-dark font-display fs-5">{{ $match->away_team_name }}</span>
                            <div class="text-muted small mt-1"><i class="fa-solid fa-landmark me-1"></i> Sân: {{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân chính') }}</div>
                        </div>
                        <div>
                            @if($match->status === 'LIVE')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1">LIVE</span>
                            @elseif($match->status === 'FINISHED')
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1">KẾT THÚC</span>
                            @else
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">SẮP ĐẤU</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Thời gian thi đấu</label>
                        <input type="datetime-local" name="match_time" class="form-control rounded-3" value="{{ $match->match_time ? date('Y-m-d\TH:i', strtotime($match->match_time)) : '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Trạng thái trận đấu</label>
                        <select name="status" class="form-select rounded-3" required>
                            <option value="UPCOMING" {{ $match->status === 'UPCOMING' ? 'selected' : '' }}>Sắp diễn ra (UPCOMING)</option>
                            <option value="LIVE" {{ $match->status === 'LIVE' ? 'selected' : '' }}>Đang diễn ra (LIVE)</option>
                            <option value="FINISHED" {{ $match->status === 'FINISHED' ? 'selected' : '' }}>Đã kết thúc (FINISHED)</option>
                            <option value="CANCELLED" {{ $match->status === 'CANCELLED' ? 'selected' : '' }}>Hủy bỏ (CANCELLED)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bàn thắng {{ $match->home_team_name }}</label>
                            <input type="number" name="home_score" class="form-control rounded-3" value="{{ $match->home_score ?? 0 }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bàn thắng {{ $match->away_team_name }}</label>
                            <input type="number" name="away_score" class="form-control rounded-3" value="{{ $match->away_score ?? 0 }}" min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/matches') }}" class="btn btn-secondary btn-sm rounded-pill px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="fa-solid fa-check me-1"></i> Cập Nhật Trận Đấu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
