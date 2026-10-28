@extends('layouts.admin')

@section('title', 'Quản Lý Người Dùng & Phân Quyền - FIFA World Cup 2030')
@section('page_title', 'Danh Sách Người Dùng & Phân Quyền Tài Khoản')

@section('content')
<div class="admin-card mb-4">
    <div class="p-3">
        <form action="{{ url('/admin/users') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm theo tên, email hoặc số điện thoại..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm rounded-pill px-3">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="USER" {{ request('role') === 'USER' ? 'selected' : '' }}>Khách hàng (USER)</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>Quản trị viên (ADMIN)</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fa-solid fa-search me-1"></i> Tìm</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-rotate-left"></i> Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="p-0">
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
                        <td class="ps-3 fw-bold text-muted">#{{ $u->id }}</td>
                        <td class="fw-bold text-dark">
                            <i class="fa-solid fa-circle-user me-1 text-primary"></i> {{ $u->name }}
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone ?? 'Chưa cập nhật' }}</td>
                        <td>
                            @if($u->role === 'ADMIN')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                    <i class="fa-solid fa-shield-halved me-1"></i>ADMIN
                                </span>
                            @else
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                    <i class="fa-solid fa-user me-1"></i>USER
                                </span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>
                        <td class="text-end pe-3">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <i class="fa-solid fa-user-gear me-1"></i> Sửa vai trò
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Role Modal -->
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                <form action="{{ url('/admin/users/' . $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold">Phân Quyền Cho Người Dùng #{{ $u->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Họ và tên</label>
                                            <input type="text" class="form-control rounded-3" value="{{ $u->name }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Email</label>
                                            <input type="email" class="form-control rounded-3" value="{{ $u->email }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control rounded-3" value="{{ $u->phone }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Vai trò hệ thống</label>
                                            <select name="role" class="form-select rounded-3" required>
                                                <option value="USER" {{ $u->role === 'USER' ? 'selected' : '' }}>Khách hàng (USER)</option>
                                                <option value="ADMIN" {{ $u->role === 'ADMIN' ? 'selected' : '' }}>Quản trị viên (ADMIN)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Cập nhật vai trò</button>
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
