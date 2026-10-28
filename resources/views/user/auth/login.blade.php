@extends('layouts.app')

@section('title', 'Đăng Nhập - FIFA World Cup 2030')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 bg-white overflow-hidden" style="box-shadow: 0 16px 36px rgba(11, 19, 40, 0.08) !important;">
                <div class="card-header text-center py-4 text-white" style="background: radial-gradient(circle at 50% 20%, rgba(245, 158, 11, 0.2) 0%, transparent 60%), linear-gradient(135deg, #060a17 0%, #0b1328 100%); border-bottom: 3px solid #f59e0b;">
                    <div class="wc-brand-trophy mx-auto mb-2" style="width: 46px; height: 46px; font-size: 1.3rem;">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h3 class="font-display fw-bold mb-0 text-white fs-4">ĐĂNG NHẬP</h3>
                    <small class="text-gold text-uppercase fw-semibold" style="letter-spacing: 0.08em; font-size: 0.72rem; color: #f59e0b;">Cổng Đặt Vé FIFA World Cup 2030</small>
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

                        <button type="submit" class="btn btn-wc-gold w-100 py-3 mb-3 shadow">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            <span>Đăng Nhập</span>
                        </button>

                        <div class="text-center small text-secondary">
                            Chưa có tài khoản? <a href="{{ url('/register') }}" class="text-warning fw-bold text-decoration-none">Đăng ký ngay</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
