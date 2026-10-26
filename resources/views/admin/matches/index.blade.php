@extends('layouts.admin')

@section('title', 'Quản Lý Lịch Đấu & Ghế - FIFA World Cup 2030')
@section('page_title', 'Danh Sách Lịch Thi Đấu & Ghế Ngồi')

@section('content')
<div class="admin-card mb-4">
    <div class="p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-8">
                <form action="{{ url('/admin/matches') }}" method="GET" class="row g-2">
                    <div class="col-md-4">
                        <select name="status" class="form-select form-select-sm rounded-pill px-3">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="UPCOMING" {{ request('status') === 'UPCOMING' ? 'selected' : '' }}>Sắp diễn ra (UPCOMING)</option>
                            <option value="LIVE" {{ request('status') === 'LIVE' ? 'selected' : '' }}>Đang diễn ra (LIVE)</option>
                            <option value="FINISHED" {{ request('status') === 'FINISHED' ? 'selected' : '' }}>Đã kết thúc (FINISHED)</option>
                            <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Đã hủy (CANCELLED)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control form-control-sm rounded-pill px-3" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
                        @if(request()->hasAny(['status', 'date']))
                            <a href="{{ url('/admin/matches') }}" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-rotate-left"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="col-md-4 text-md-end">
                <button type="button" class="btn btn-warning btn-sm fw-bold text-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createMatchModal">
                    <i class="fa-solid fa-plus me-1"></i> Lập Lịch Trận Mới
                </button>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">Trận #</th>
                        <th>Giai đoạn</th>
                        <th>Cặp đấu</th>
                        <th>Thời gian</th>
                        <th>Sân vận động</th>
                        <th>Tỷ số</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($matches as $match)
                    <tr>
                        <td class="ps-3 fw-bold text-primary">#{{ $match->match_number ?? $match->id }}</td>
                        <td><span class="badge rounded-pill bg-light text-secondary border px-2 py-1">{{ $match->stage ?? 'GROUP' }}</span></td>
                        <td class="fw-bold text-dark font-display">
                            {{ $match->home_team_name }}
                            <span class="text-warning mx-1">vs</span>
                            {{ $match->away_team_name }}
                        </td>
                        <td>{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định' }}</td>
                        <td>{{ $match->stadium->stadium_name ?? $match->stadium->name }}</td>
                        <td>
                            @if($match->status === 'FINISHED' || $match->status === 'LIVE')
                                <strong class="text-danger">{{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($match->status === 'LIVE')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">LIVE</span>
                            @elseif($match->status === 'FINISHED')
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">KẾT THÚC</span>
                            @else
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">SẮP ĐẤU</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ url('/matches/' . $match->id) }}" target="_blank" class="btn btn-sm btn-outline-info btn-action-icon me-1" title="Xem sơ đồ ghế">
                                <i class="fa-solid fa-chair"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-action-icon me-1" data-bs-toggle="modal" data-bs-target="#editMatchModal{{ $match->id }}" title="Chỉnh sửa tỷ số/trạng thái">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ url('/admin/matches/' . $match->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa/hủy trận đấu này cùng toàn bộ ghế đã tạo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action-icon" title="Hủy trận đấu">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editMatchModal{{ $match->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ url('/admin/matches/' . $match->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Cập Nhật Trận Đấu #{{ $match->match_number ?? $match->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
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
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Bàn thắng {{ $match->home_team_name }}</label>
                                                <input type="number" name="home_score" class="form-control rounded-3" value="{{ $match->home_score ?? 0 }}" min="0">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Bàn thắng {{ $match->away_team_name }}</label>
                                                <input type="number" name="away_score" class="form-control rounded-3" value="{{ $match->away_score ?? 0 }}" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Cập nhật</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy trận đấu nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $matches->links('pagination::bootstrap-5') }}
</div>

<!-- Create Modal -->
<div class="modal fade" id="createMatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/admin/matches') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white" style="background: #0b132b !important;">
                    <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-calendar-plus me-1"></i> Lập Lịch Trận Đấu Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Số thứ tự trận (Tùy chọn)</label>
                            <input type="number" name="match_number" class="form-control" placeholder="Tự sinh nếu trống">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Giai đoạn thi đấu</label>
                            <select name="stage" class="form-select" required>
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
                            <select name="home_team_id" class="form-select" required>
                                <option value="">-- Chọn đội nhà --</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Đội khách (Away Team)</label>
                            <select name="away_team_id" class="form-select" required>
                                <option value="">-- Chọn đội khách --</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sân vận động</label>
                            <select name="stadium_id" class="form-select" required>
                                <option value="">-- Chọn sân đăng cai --</option>
                                @foreach($stadiums as $s)
                                    <option value="{{ $s->id }}">{{ $s->stadium_name ?? $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Thời gian diễn ra</label>
                            <input type="datetime-local" name="match_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 small mt-3 mb-0">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Sau khi tạo trận đấu, hệ thống sẽ <strong>tự động sinh toàn bộ danh sách ghế</strong> cho trận để phục vụ khách đặt vé ngay lập tức.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark">Lập Lịch & Sinh Ghế</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
