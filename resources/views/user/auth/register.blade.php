@extends('layouts.app')

@section('title', 'Đăng Ký Tài Khoản - FIFA World Cup 2030')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 bg-white overflow-hidden">
                <div class="card-header text-center py-4 text-white" style="background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%); border-bottom: 3px solid #ffb703;">
                    <i class="fa-solid fa-user-plus text-warning fa-2x mb-2"></i>
                    <h4 class="fw-bold mb-0 text-white">ĐĂNG KÝ TÀI KHOẢN</h4>
                    <small class="text-light opacity-75">Tạo tài khoản để đặt vé và nhận vé điện tử nhanh chóng</small>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ url('/register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small"><i class="fa-solid fa-user text-secondary me-1"></i> Họ và tên</label>
                            <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small"><i class="fa-solid fa-envelope text-secondary me-1"></i> Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nguyenvana@gmail.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small"><i class="fa-solid fa-phone text-secondary me-1"></i> Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="0901234567" value="{{ old('phone') }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small"><i class="fa-solid fa-lock text-secondary me-1"></i> Mật khẩu (tối thiểu 6 ký tự)</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-dark shadow-sm mb-3">
                            <i class="fa-solid fa-check me-1"></i> Hoàn Tất Đăng Ký
                        </button>

                        <div class="text-center small text-secondary">
                            Đã có tài khoản? <a href="{{ url('/login') }}" class="text-primary fw-bold text-decoration-none">Đăng nhập</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
