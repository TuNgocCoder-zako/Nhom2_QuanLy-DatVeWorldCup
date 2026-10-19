@extends('layouts.admin')

@section('title', 'Quản Lý Người Dùng & Phân Quyền - FIFA World Cup 2030')
@section('page_title', 'Danh Sách Người Dùng & Phân Quyền Tài Khoản')

@section('content')
<div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
    <div class="card-body p-3">
        <form action="{{ url('/admin/users') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm theo tên, email hoặc số điện thoại..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="USER" {{ request('role') === 'USER' ? 'selected' : '' }}>Khách hàng (USER)</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>Quản trị viên (ADMIN)</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-search me-1"></i> Tìm</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-rotate-left"></i> Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end pe-3">Phân quyền</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($users as $u)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">{{ $u->id }}</td>
                        <td class="fw-bold text-dark">
                            <i class="fa-solid fa-user-circle me-1 text-secondary"></i> {{ $u->name }}
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                        <td>
                            <span class="badge {{ $u->role === 'ADMIN' ? 'bg-danger' : 'bg-primary' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>
                        <td class="text-end pe-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <i class="fa-solid fa-user-gear me-1"></i> Sửa vai trò
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Role Modal -->
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ url('/admin/users/' . $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Phân Quyền Cho Người Dùng #{{ $u->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Họ và tên</label>
                                            <input type="text" class="form-control" value="{{ $u->name }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Email</label>
                                            <input type="email" class="form-control" value="{{ $u->email }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Vai trò hệ thống</label>
                                            <select name="role" class="form-select" required>
                                                <option value="USER" {{ $u->role === 'USER' ? 'selected' : '' }}>Khách hàng (USER)</option>
                                                <option value="ADMIN" {{ $u->role === 'ADMIN' ? 'selected' : '' }}>Quản trị viên (ADMIN)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Cập nhật vai trò</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy người dùng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
@endsection
