@extends('layouts.admin')

@section('title', 'Quản Lý Đội Tuyển - FIFA World Cup 2030')
@section('page_title', 'Danh Sách Các Đội Tuyển Tham Dự')

@section('content')
<div class="admin-card mb-4">
    <div class="p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-8">
                <form action="{{ url('/admin/teams') }}" method="GET" class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm theo tên hoặc mã quốc gia..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="group" class="form-select form-select-sm rounded-pill px-3">
                            <option value="">-- Tất cả bảng --</option>
                            @foreach($groups as $grp)
                                <option value="{{ $grp }}" {{ request('group') === $grp ? 'selected' : '' }}>Bảng {{ $grp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fa-solid fa-search me-1"></i> Tìm</button>
                        @if(request()->hasAny(['search', 'group']))
                            <a href="{{ url('/admin/teams') }}" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-rotate-left"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="col-md-4 text-md-end">
                <button type="button" class="btn btn-warning btn-sm fw-bold text-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createTeamModal">
                    <i class="fa-solid fa-plus me-1"></i> Thêm Đội Tuyển
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Teams Table -->
<div class="admin-card">
    <div class="p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Quốc kỳ</th>
                        <th>Tên đội tuyển</th>
                        <th>Mã FIFA</th>
                        <th>Bảng đấu</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($teams as $team)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $team->id }}</td>
                        <td>
                            @if($team->flag_url)
                                <img src="{{ $team->flag_url }}" alt="{{ $team->name }}" style="width: 32px; height: 22px; object-fit: cover; border-radius: 4px;" class="shadow-sm">
                            @else
                                <span class="badge rounded-pill bg-secondary">N/A</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark font-display">{{ $team->name }}</td>
                        <td><span class="badge rounded-pill bg-light text-dark border">{{ $team->code }}</span></td>
                        <td><span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Bảng {{ $team->group_name }}</span></td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-action-icon me-1" data-bs-toggle="modal" data-bs-target="#editTeamModal{{ $team->id }}" title="Chỉnh sửa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ url('/admin/teams/' . $team->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đội tuyển này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action-icon" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editTeamModal{{ $team->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ url('/admin/teams/' . $team->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Chỉnh Sửa Đội Tuyển #{{ $team->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Tên đội tuyển</label>
                                            <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Mã FIFA (3 ký tự)</label>
                                                <input type="text" name="code" class="form-control text-uppercase" maxlength="3" value="{{ $team->code }}" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Bảng đấu</label>
                                                <select name="group_name" class="form-select" required>
                                                    @foreach($groups as $grp)
                                                        <option value="{{ $grp }}" {{ $team->group_name === $grp ? 'selected' : '' }}>Bảng {{ $grp }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">URL Quốc kỳ</label>
                                            <input type="url" name="flag_url" class="form-control" value="{{ $team->flag_url }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy đội tuyển nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $teams->links('pagination::bootstrap-5') }}
</div>

<!-- Create Modal -->
<div class="modal fade" id="createTeamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/admin/teams') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white" style="background: #0b132b !important;">
                    <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-plus me-1"></i> Thêm Đội Tuyển Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên đội tuyển</label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: Tây Ban Nha" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mã FIFA (3 ký tự)</label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="ESP" maxlength="3" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bảng đấu</label>
                            <select name="group_name" class="form-select" required>
                                @foreach($groups as $grp)
                                    <option value="{{ $grp }}">Bảng {{ $grp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">URL Quốc kỳ (Tùy chọn)</label>
                        <input type="url" name="flag_url" class="form-control" placeholder="https://flagcdn.com/w80/es.png">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark">Thêm Đội Tuyển</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
