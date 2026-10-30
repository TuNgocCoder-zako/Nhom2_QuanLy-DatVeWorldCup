<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', 'Admin - FIFA World Cup 2030')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom Admin Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('styles')
</head>
<body class="d-flex">
    <!-- Admin Sidebar -->
    @include('layouts.admin-sidebar')

    <!-- Admin Content Area -->
    <div class="flex-grow-1 d-flex flex-column min-vh-100" style="background-color: var(--admin-bg);">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <div>
                <span class="text-secondary small fw-semibold d-block" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">Trung Tâm Quản Trị Giải Đấu</span>
                <h4 class="font-display fw-bold mb-0 text-dark">
                    @yield('page_title', 'Bảng Điều Khiển')
                </h4>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="admin-badge-role">ADMINISTRATOR</span>
                <div class="d-flex align-items-center gap-2 border-start ps-3">
                    <span class="admin-user-avatar">
                        <i class="fa-solid fa-user-shield"></i>
                    </span>
                    <div class="d-none d-sm-block">
                        <strong class="text-dark small d-block font-display" style="line-height: 1.2;">{{ session('user_name') ?? 'Ban Quản Trị' }}</strong>
                        <span class="text-success small" style="font-size: 0.72rem;"><i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i>Trực tuyến</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center rounded-4" role="alert" style="background-color: #ecfdf5; border-left: 4px solid #10b981 !important; color: #065f46;">
                    <i class="fa-solid fa-circle-check fs-5 me-2 text-success"></i>
                    <div class="flex-grow-1 fw-medium">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center rounded-4" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                    <i class="fa-solid fa-circle-exclamation fs-5 me-2 text-danger"></i>
                    <div class="flex-grow-1 fw-medium">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fa-solid fa-circle-xmark fs-5 me-2 text-danger"></i>
                        <strong>Vui lòng kiểm tra lại:</strong>
                    </div>
                    <ul class="mb-0 ps-4 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Main Page Content -->
        <main class="container-fluid px-4 py-4 flex-grow-1">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
