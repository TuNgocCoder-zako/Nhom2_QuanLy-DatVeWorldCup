@extends('layouts.admin')

@section('title', 'Quản Lý Sân Vận Động - FIFA World Cup 2030')
@section('page_title', 'Danh Sách Sân Vận Động Đăng Cai')

@section('content')
<div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-landmark text-primary me-2"></i> Các sân vận động tổ chức World Cup 2030</h6>
            <small class="text-secondary">Mỗi sân được tự động phân bổ 4 khu vực khán đài tiêu chuẩn A, B, C, D</small>
        </div>
        <button type="button" class="btn btn-warning btn-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#createStadiumModal">
            <i class="fa-solid fa-plus me-1"></i> Thêm Sân Vận Động
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Tên sân vận động</th>
                        <th>Thành phố & Địa điểm</th>
                        <th>Sức chứa</th>
                        <th>Khu vực ghế</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($stadiums as $stadium)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">{{ $stadium->id }}</td>
                        <td class="fw-bold text-dark">
                            <i class="fa-solid fa-building-columns text-warning me-1"></i>
                            {{ $stadium->stadium_name ?? $stadium->name }}
                        </td>
                        <td>
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            {{ $stadium->location ?? 'Tây Ban Nha' }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ number_format((int)$stadium->capacity) }} chỗ</span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $stadium->seatZones->count() }} khu vực</span>
                        </td>
                        <td class="text-end pe-3">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editStadiumModal{{ $stadium->id }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ url('/admin/stadiums/' . $stadium->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sân này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editStadiumModal{{ $stadium->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ url('/admin/stadiums/' . $stadium->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Chỉnh Sửa Sân Vận Động #{{ $stadium->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Tên sân vận động</label>
                                            <input type="text" name="name" class="form-control" value="{{ $stadium->stadium_name ?? $stadium->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Địa điểm / Quốc gia</label>
                                            <input type="text" name="location" class="form-control" value="{{ $stadium->location }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Sức chứa (chỗ ngồi)</label>
                                            <input type="number" name="capacity" class="form-control" value="{{ $stadium->capacity }}" required min="1000">
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
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có sân vận động nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $stadiums->links('pagination::bootstrap-5') }}
</div>

<!-- Create Modal -->
<div class="modal fade" id="createStadiumModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/admin/stadiums') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white" style="background: #0b132b !important;">
                    <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-plus me-1"></i> Thêm Sân Vận Động Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên sân vận động</label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: Santiago Bernabéu" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa điểm / Quốc gia</label>
                        <input type="text" name="location" class="form-control" placeholder="Madrid, Tây Ban Nha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sức chứa dự kiến</label>
                        <input type="number" name="capacity" class="form-control" placeholder="85000" min="1000" required>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fa-solid fa-info-circle me-1"></i> Hệ thống sẽ tự động tạo 4 khu vực khán đài (Khán đài A VIP, Khán đài B, C, D) cho sân mới.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark">Thêm Sân Vận Động</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
