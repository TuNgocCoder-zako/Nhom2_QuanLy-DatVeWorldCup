@extends('layouts.admin')

@section('title', 'Thêm Đội Tuyển Mới - FIFA World Cup 2030')
@section('page_title', 'Thêm Đội Tuyển Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-flag text-warning me-2"></i> Nhập Thông Tin Đội Tuyển</h6>
                <a href="{{ url('/admin/teams') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="p-4">
                <form action="{{ url('/admin/teams') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên đội tuyển</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Ví dụ: Tây Ban Nha" value="{{ old('name') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mã FIFA (3 ký tự viết hoa)</label>
                            <input type="text" name="code" class="form-control rounded-3 text-uppercase" placeholder="ESP" maxlength="3" value="{{ old('code') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Bảng đấu (A - L)</label>
                            <select name="group_name" class="form-select rounded-3" required>
                                @foreach(['A','B','C','D','E','F','G','H','I','J','K','L'] as $grp)
                                    <option value="{{ $grp }}" {{ old('group_name') === $grp ? 'selected' : '' }}>Bảng {{ $grp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">URL Quốc kỳ (Tùy chọn)</label>
                        <input type="url" name="flag_url" class="form-control rounded-3" placeholder="https://flagcdn.com/w80/es.png" value="{{ old('flag_url') }}">
                        <small class="text-muted">Nếu để trống, hệ thống sẽ tự động tạo URL cờ theo mã quốc gia.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/teams') }}" class="btn btn-secondary btn-sm rounded-pill px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark rounded-pill px-4">
                            <i class="fa-solid fa-check me-1"></i> Lưu Đội Tuyển
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
