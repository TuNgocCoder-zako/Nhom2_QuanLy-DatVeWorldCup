@extends('layouts.app')

@section('title', 'Đăng Nhập - FIFA World Cup 2030')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 bg-white overflow-hidden">
                <div class="card-header text-center py-4 text-white" style="background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%); border-bottom: 3px solid #ffb703;">
                    <i class="fa-solid fa-trophy text-warning fa-2x mb-2"></i>
                    <h4 class="fw-bold mb-0 text-white">ĐĂNG NHẬP</h4>
                    <small class="text-light opacity-75">Hệ Thống Đặt Vé FIFA World Cup 2030</small>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small"><i class="fa-solid fa-envelope text-secondary me-1"></i> Email</label>
                            <input type="email" name="email" class="form-control" placeholder="user@gmail.com" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small"><i class="fa-solid fa-lock text-secondary me-1"></i> Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-dark shadow-sm mb-3">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng Nhập
                        </button>

                        <div class="text-center small text-secondary">
                            Chưa có tài khoản? <a href="{{ url('/register') }}" class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a>
                        </div>
                    </form>

                    <!-- Demo Credentials Card -->
                    <div class="mt-4 p-3 bg-light rounded-3 border">
                        <div class="fw-bold text-dark small mb-2"><i class="fa-solid fa-key text-warning me-1"></i> Tài khoản Demo có sẵn:</div>
                        <div class="small text-secondary mb-1">
                            &bull; <strong>Quản trị viên:</strong> <code>admin@worldcup2030.com</code> | Pass: <code>123456</code>
                        </div>
                        <div class="small text-secondary">
                            &bull; <strong>Khách hàng:</strong> <code>user@gmail.com</code> | Pass: <code>123456</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
