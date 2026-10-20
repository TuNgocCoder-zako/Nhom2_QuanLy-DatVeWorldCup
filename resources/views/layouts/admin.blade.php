<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - FIFA World Cup 2030')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        h1, h2, h3, h4, h5, h6, .fw-bold {
            font-family: 'Montserrat', sans-serif;
        }
        .admin-main {
            flex-grow: 1;
            min-height: 100vh;
            background-color: #f4f6f9;
        }
        .admin-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 24px;
        }
    </style>

    @yield('styles')
</head>
<body class="d-flex">
    <!-- Admin Sidebar -->
    @include('layouts.admin-sidebar')

    <!-- Admin Content Area -->
    <div class="admin-main d-flex flex-column">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center shadow-sm">
            <h5 class="fw-bold mb-0 text-dark">
                @yield('page_title', 'Hệ Thống Quản Trị')
            </h5>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-danger">ADMINISTRATOR</span>
                <span class="text-dark fw-bold small">
                    <i class="fa-solid fa-user-shield me-1 text-primary"></i>
                    {{ session('user_name') ?? 'Admin' }}
                </span>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-xmark me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Main Page Content -->
        <main class="container-fluid px-4 py-3 flex-grow-1">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
