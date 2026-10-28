<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FIFA World Cup 2030 - Hệ Thống Đặt Vé Chính Thức')</title>
    <meta name="description" content="Cổng thông tin và đặt vé chính thức giải bóng đá vô địch thế giới FIFA World Cup 2030 tại Tây Ban Nha, Bồ Đào Nha và Ma-rốc. Kỷ niệm 100 năm lịch sử World Cup.">

    <!-- Google Fonts: Plus Jakarta Sans (Headings) & Inter (UI/Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro/Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header Navbar -->
    @include('layouts.header')

    <!-- Flash Notifications -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert" style="background-color: #ecfdf5; border-left: 4px solid #10b981 !important; color: #065f46;">
                <i class="fa-solid fa-circle-check fs-5 me-2 text-success"></i>
                <div class="flex-grow-1 fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2 text-danger"></i>
                <div class="flex-grow-1 fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert" style="background-color: #fffbeb; border-left: 4px solid #f59e0b !important; color: #92400e;">
                <i class="fa-solid fa-triangle-exclamation fs-5 me-2 text-warning"></i>
                <div class="flex-grow-1 fw-medium">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                <div class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-circle-xmark fs-5 me-2 text-danger"></i>
                    <strong>Vui lòng kiểm tra lại các thông tin:</strong>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1 pb-5">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
