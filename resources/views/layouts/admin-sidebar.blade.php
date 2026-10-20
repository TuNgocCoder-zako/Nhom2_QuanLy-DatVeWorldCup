<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark shadow" style="width: 260px; min-height: 100vh; background: #0b132b !important; border-right: 1px solid rgba(255,255,255,0.1);">
    <a href="{{ url('/admin/dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-warning text-decoration-none fw-bold fs-5">
        <i class="fa-solid fa-trophy me-2"></i>
        <span>WC 2030 ADMIN</span>
    </a>
    <hr class="border-secondary opacity-25">
    
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        <li class="nav-item">
            <a href="{{ url('/admin/dashboard') }}" class="nav-link text-white {{ request()->is('admin/dashboard*') ? 'active bg-warning text-dark fw-bold' : '' }}">
                <i class="fa-solid fa-gauge-high me-2"></i> Bảng điều khiển
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/teams') }}" class="nav-link text-white {{ request()->is('admin/teams*') ? 'active bg-warning text-dark fw-bold' : '' }}">
                <i class="fa-solid fa-flag me-2"></i> Quản lý Đội tuyển
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/stadiums') }}" class="nav-link text-white {{ request()->is('admin/stadiums*') ? 'active bg-warning text-dark fw-bold' : '' }}">
                <i class="fa-solid fa-landmark me-2"></i> Sân vận động
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/matches') }}" class="nav-link text-white {{ request()->is('admin/matches*') ? 'active bg-warning text-dark fw-bold' : '' }}">
                <i class="fa-solid fa-futbol me-2"></i> Lịch đấu & Tạo ghế
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/orders') }}" class="nav-link text-white {{ request()->is('admin/orders*') ? 'active bg-warning text-dark fw-bold' : '' }}">
                <i class="fa-solid fa-receipt me-2"></i> Đơn hàng & Vé
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/users') }}" class="nav-link text-white {{ request()->is('admin/users*') ? 'active bg-warning text-dark fw-bold' : '' }}">
                <i class="fa-solid fa-users me-2"></i> Người dùng & Phân quyền
            </a>
        </li>
    </ul>

    <hr class="border-secondary opacity-25">
    
    <div class="d-flex flex-column gap-2">
        <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm text-start">
            <i class="fa-solid fa-globe me-2"></i> Xem Trang Chủ
        </a>
        <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm text-start w-100">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
            </button>
        </form>
    </div>
</div>
