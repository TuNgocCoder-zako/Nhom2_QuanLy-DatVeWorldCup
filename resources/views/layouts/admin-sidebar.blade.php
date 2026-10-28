<aside class="admin-sidebar d-flex flex-column flex-shrink-0 p-3 text-white">
    <!-- Brand Header -->
    <a href="{{ url('/admin/dashboard') }}" class="admin-sidebar-brand">
        <span class="admin-brand-icon" aria-hidden="true">
            <i class="fa-solid fa-trophy"></i>
        </span>
        <div class="d-flex flex-column">
            <span class="font-display fw-bold text-white fs-6" style="letter-spacing: -0.01em;">FIFA 2030</span>
            <span class="text-warning text-uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;">Command Center</span>
        </div>
    </a>

    <div class="my-3 px-2">
        <small class="text-uppercase fw-bold text-secondary" style="font-size: 0.68rem; letter-spacing: 0.08em;">Quản trị hệ thống</small>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ url('/admin/dashboard') }}" class="admin-nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Bảng điều khiển</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/teams') }}" class="admin-nav-link {{ request()->is('admin/teams*') ? 'active' : '' }}">
                <i class="fa-solid fa-flag"></i>
                <span>Quản lý Đội tuyển</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/stadiums') }}" class="admin-nav-link {{ request()->is('admin/stadiums*') ? 'active' : '' }}">
                <i class="fa-solid fa-landmark"></i>
                <span>Sân vận động</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/matches') }}" class="admin-nav-link {{ request()->is('admin/matches*') ? 'active' : '' }}">
                <i class="fa-solid fa-futbol"></i>
                <span>Lịch đấu & Tạo ghế</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/orders') }}" class="admin-nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Đơn hàng & Vé</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/users') }}" class="admin-nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Người dùng & Quyền</span>
            </a>
        </li>
    </ul>

    <hr class="border-secondary border-opacity-25 my-3">

    <!-- Bottom Actions -->
    <div class="d-flex flex-column gap-2 px-1">
        <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm text-start py-2 d-flex align-items-center gap-2 rounded-pill" style="border-color: rgba(255,255,255,0.2);">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            <span>Xem Trang Khách</span>
        </a>
        <form action="{{ url('/logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm text-start w-100 py-2 d-flex align-items-center gap-2 rounded-pill" style="background: rgba(225, 29, 72, 0.2); border: 1px solid rgba(225, 29, 72, 0.4); color: #ff6b81;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>
