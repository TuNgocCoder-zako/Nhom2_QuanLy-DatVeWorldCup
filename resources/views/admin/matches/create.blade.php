@extends('layouts.admin')

@section('title', 'Lập Lịch Trận Đấu Mới - FIFA World Cup 2030')
@section('page_title', 'Lập Lịch Trận Đấu & Sinh Ghế')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-calendar-plus text-warning me-2"></i> Lập Lịch Trận Đấu Mới</h6>
                <a href="{{ url('/admin/matches') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="p-4">
                <form action="{{ url('/admin/matches') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Số thứ tự trận</label>
                            <input type="number" name="match_number" class="form-control rounded-3" placeholder="Tự sinh nếu để trống">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Giai đoạn thi đấu</label>
                            <select name="stage" class="form-select rounded-3" required>
                                <option value="GROUP">Vòng bảng (GROUP)</option>
                                <option value="ROUND_32">Vòng 32 đội (ROUND_32)</option>
                                <option value="ROUND_16">Vòng 16 đội (ROUND_16)</option>
                                <option value="QUARTER_FINAL">Tứ kết (QUARTER_FINAL)</option>
                                <option value="SEMI_FINAL">Bán kết (SEMI_FINAL)</option>
                                <option value="THIRD_PLACE">Tranh hạng ba (THIRD_PLACE)</option>
                                <option value="FINAL">Chung kết (FINAL)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Đội nhà (Home Team)</label>
                            <select name="home_team_id" class="form-select rounded-3" required>
                                <option value="">-- Chọn đội nhà --</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Đội khách (Away Team)</label>
                            <select name="away_team_id" class="form-select rounded-3" required>
                                <option value="">-- Chọn đội khách --</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sân vận động</label>
                            <select name="stadium_id" class="form-select rounded-3" required>
                                <option value="">-- Chọn sân đăng cai --</option>
                                @foreach($stadiums as $s)
                                    <option value="{{ $s->id }}">{{ $s->stadium_name ?? $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Thời gian diễn ra</label>
                            <input type="datetime-local" name="match_time" class="form-control rounded-3" required>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small mt-3 mb-4 rounded-3">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Sau khi tạo trận đấu, hệ thống sẽ <strong>tự động sinh toàn bộ danh sách ghế</strong> cho trận để phục vụ khách đặt vé ngay lập tức.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/matches') }}" class="btn btn-secondary btn-sm rounded-pill px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark rounded-pill px-4">
                            <i class="fa-solid fa-check me-1"></i> Lập Lịch & Sinh Ghế
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
