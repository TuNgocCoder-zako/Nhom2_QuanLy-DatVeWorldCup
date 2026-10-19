@extends('layouts.admin')

@section('title', 'Phân Quyền Người Dùng - FIFA World Cup 2030')
@section('page_title', 'Phân Quyền Người Dùng #' . $user->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-gear text-primary me-2"></i> Chỉnh Sửa Người Dùng & Phân Quyền</h6>
                <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('/admin/users/' . $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Họ và tên</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Email đăng nhập</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Vai trò hệ thống</label>
                        <select name="role" class="form-select" required>
                            <option value="USER" {{ old('role', $user->role) === 'USER' ? 'selected' : '' }}>Khách hàng (USER)</option>
                            <option value="ADMIN" {{ old('role', $user->role) === 'ADMIN' ? 'selected' : '' }}>Quản trị viên (ADMIN)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/users') }}" class="btn btn-secondary btn-sm">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fa-solid fa-check me-1"></i> Cập Nhật Vai Trò
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
