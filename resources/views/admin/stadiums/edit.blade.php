@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sân Vận Động - FIFA World Cup 2030')
@section('page_title', 'Chỉnh Sửa Sân Vận Động')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Chỉnh Sửa Sân #{{ $stadium->id }}</h6>
                <a href="{{ url('/admin/stadiums') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ url('/admin/stadiums/' . $stadium->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên sân vận động</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $stadium->stadium_name ?? $stadium->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa điểm / Quốc gia</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $stadium->location) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Sức chứa (chỗ ngồi)</label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $stadium->capacity) }}" min="1000" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url('/admin/stadiums') }}" class="btn btn-secondary btn-sm">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fa-solid fa-check me-1"></i> Cập Nhật Sân
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
