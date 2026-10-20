<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-2" style="background: linear-gradient(90deg, #0b132b 0%, #1c2541 100%) !important; border-bottom: 2px solid #f77f00;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ url('/') }}" style="color: #ffb703; font-size: 1.25rem;">
            <i class="fa-solid fa-trophy text-warning"></i>
            <span>FIFA WORLD CUP 2030</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active text-warning' : 'text-light' }}" href="{{ url('/') }}">
                        <i class="fa-solid fa-house me-1"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('matches*') ? 'active text-warning' : 'text-light' }}" href="{{ url('/matches') }}">
                        <i class="fa-solid fa-futbol me-1"></i> Lịch thi đấu & Đặt vé
                    </a>
                </li>
                @if(session('user_id') || (isset($_SESSION['user_id']) && $_SESSION['user_id']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('my-tickets*') ? 'active text-warning' : 'text-light' }}" href="{{ url('/my-tickets') }}">
                        <i class="fa-solid fa-ticket me-1"></i> Vé của tôi
                    </a>
                </li>
                @endif
                @if(session('user_role') === 'ADMIN' || (isset($_SESSION['user_id']) && \App\Models\User::find($_SESSION['user_id'])?->isAdmin()))
                <li class="nav-item">
                    <a class="nav-link text-warning fw-bold" href="{{ url('/admin/dashboard') }}">
                        <i class="fa-solid fa-gauge-high me-1"></i> Quản trị (Admin)
                    </a>
                </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                @php
                    $curUserId = session('user_id') ?? ($_SESSION['user_id'] ?? null);
                    $currentUser = $curUserId ? \App\Models\User::find($curUserId) : null;
                @endphp

                @if($currentUser)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-user"></i></span>
                        <span>{{ $currentUser->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="{{ url('/my-tickets') }}"><i class="fa-solid fa-ticket me-2 text-primary"></i>Vé đã đặt</a></li>
                        @if($currentUser->isAdmin())
                        <li><a class="dropdown-item" href="{{ url('/admin/dashboard') }}"><i class="fa-solid fa-shield-halved me-2 text-danger"></i>Trang quản trị</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ url('/logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="btn btn-outline-warning btn-sm px-3" href="{{ url('/login') }}">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Đăng nhập
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-warning btn-sm px-3 fw-bold text-dark" href="{{ url('/register') }}">
                        <i class="fa-solid fa-user-plus me-1"></i> Đăng ký
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
