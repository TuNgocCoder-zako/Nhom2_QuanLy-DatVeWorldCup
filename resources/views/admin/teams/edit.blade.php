@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Đội Tuyển - FIFA World Cup 2030')
@section('page_title', 'Chỉnh Sửa Đội Tuyển')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Chỉnh Sửa Đội Tuyển #{{ $team->id }}</h6>
                <a href="{{ url('/admin/teams') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="p-4">
                <form action="{{ url('/admin/teams/' . $team->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên đội tuyển</label>
                        <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $team->name) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mã FIFA (3 ký tự viết hoa)</label>
                            <input type="text" name="code" class="form-control rounded-3 text-uppercase" maxlength="3" value="{{ old('code', $team->code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Bảng đấu (A - L)</label>
                            <select name="group_name" class="form-select rounded-3" required>
                                @foreach(['A','B','C','D','E','F','G','H','I','J','K','L'] as $grp)
                                    <option value="{{ $grp }}" {{ old('group_name', $team->group_name) === $grp ? 'selected' : '' }}>Bảng {{ $grp }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">URL Quốc kỳ</label>
                        <input type="url" name="flag_url" class="form-control rounded-3" value="{{ old('flag_url', $team->flag_url) }}">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/teams') }}" class="btn btn-secondary btn-sm rounded-pill px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="fa-solid fa-check me-1"></i> Cập Nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
