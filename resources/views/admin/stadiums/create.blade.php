@extends('layouts.admin')

@section('title', 'Thêm Sân Vận Động - FIFA World Cup 2030')
@section('page_title', 'Thêm Sân Vận Động Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-landmark text-warning me-2"></i> Thông Tin Sân Đăng Cai</h6>
                <a href="{{ url('/admin/stadiums') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="p-4">
                <form action="{{ url('/admin/stadiums') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên sân vận động</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Ví dụ: Santiago Bernabéu" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa điểm / Quốc gia</label>
                        <input type="text" name="location" class="form-control rounded-3" placeholder="Madrid, Tây Ban Nha" value="{{ old('location') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sức chứa (chỗ ngồi)</label>
                        <input type="number" name="capacity" class="form-control rounded-3" placeholder="85000" min="1000" value="{{ old('capacity') }}" required>
                    </div>

                    <div class="alert alert-info py-2 small mb-4 rounded-3">
                        <i class="fa-solid fa-info-circle me-1"></i> Hệ thống sẽ tự động khởi tạo 4 khu vực khán đài (Khán đài A VIP, Khán đài B, C, D) cho sân mới này.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/stadiums') }}" class="btn btn-secondary btn-sm rounded-pill px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark rounded-pill px-4">
                            <i class="fa-solid fa-check me-1"></i> Lưu Sân Vận Động
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
