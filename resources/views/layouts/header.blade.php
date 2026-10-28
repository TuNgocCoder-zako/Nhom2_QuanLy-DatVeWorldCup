<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark wc-navbar py-2" aria-label="Thanh điều hướng chính">
        <div class="container">
            <!-- Brand Logo -->
            <a class="wc-brand" href="{{ url('/') }}">
                <span class="wc-brand-trophy" aria-hidden="true">
                    <i class="fa-solid fa-trophy"></i>
                </span>
                <span class="d-flex flex-column">
                    <span class="d-flex align-items-center gap-2">
                        <span>FIFA WORLD CUP</span>
                        <span class="badge bg-warning text-dark fw-bold px-2 py-0" style="font-size: 0.72rem;">2030</span>
                    </span>
                    <span class="text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.12em; color: #94a3b8; font-weight: 600;">
                        Tây Ban Nha &bull; Bồ Đào Nha &bull; Ma-rốc
                    </span>
                </span>
            </a>

            <!-- Mobile Hamburger Toggle -->
            <button class="navbar-toggler border-0 shadow-none text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Mở menu điều hướng">
                <i class="fa-solid fa-bars-staggered fs-4"></i>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link wc-nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="fa-solid fa-house"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link wc-nav-link {{ request()->is('matches*') ? 'active' : '' }}" href="{{ url('/matches') }}">
                            <i class="fa-solid fa-futbol"></i>
                            <span>Lịch thi đấu & Đặt vé</span>
                        </a>
                    </li>
                    @if(session('user_id') || (isset($_SESSION['user_id']) && $_SESSION['user_id']))
                    <li class="nav-item">
                        <a class="nav-link wc-nav-link {{ request()->is('my-tickets*') ? 'active' : '' }}" href="{{ url('/my-tickets') }}">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Vé của tôi</span>
                        </a>
                    </li>
                    @endif
                    @if(session('user_role') === 'ADMIN' || (isset($_SESSION['user_id']) && \App\Models\User::find($_SESSION['user_id'])?->isAdmin()))
                    <li class="nav-item">
                        <a class="nav-link wc-nav-link text-warning fw-bold" href="{{ url('/admin/dashboard') }}" style="background: rgba(245, 158, 11, 0.1);">
                            <i class="fa-solid fa-shield-halved text-warning"></i>
                            <span>Quản trị (Admin)</span>
                        </a>
                    </li>
                    @endif
                </ul>

                <!-- Host Nations Badge & Auth Buttons -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-xl-flex wc-host-pill" title="Ba quốc gia đồng đăng cai FIFA World Cup 2030">
                        <img src="https://flagcdn.com/w20/es.png" alt="Tây Ban Nha" style="width: 16px; height: 11px; border-radius: 2px;">
                        <img src="https://flagcdn.com/w20/pt.png" alt="Bồ Đào Nha" style="width: 16px; height: 11px; border-radius: 2px;">
                        <img src="https://flagcdn.com/w20/ma.png" alt="Ma-rốc" style="width: 16px; height: 11px; border-radius: 2px;">
                        <span class="fw-semibold">Host 2030</span>
                    </div>

                    @php
                        $curUserId = session('user_id') ?? ($_SESSION['user_id'] ?? null);
                        $currentUser = $curUserId ? \App\Models\User::find($curUserId) : null;
                    @endphp

                    @if($currentUser)
                    <div class="dropdown">
                        <button class="btn btn-dark d-flex align-items-center gap-2 border-0 px-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: rgba(255,255,255,0.08); border-radius: 9999px;">
                            <span class="badge bg-warning text-dark rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-user small"></i>
                            </span>
                            <span class="text-white small fw-bold">{{ $currentUser->name }}</span>
                            <i class="fa-solid fa-chevron-down small text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="background: #0b1328; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 12px; min-width: 200px;">
                            <li class="px-3 py-2 border-bottom border-secondary border-opacity-25">
                                <small class="text-secondary d-block">Tài khoản</small>
                                <strong class="text-light small text-truncate d-block">{{ $currentUser->email }}</strong>
                            </li>
                            <li>
                                <a class="dropdown-item text-light py-2 px-3 small d-flex align-items-center gap-2" href="{{ url('/my-tickets') }}">
                                    <i class="fa-solid fa-ticket text-warning"></i>
                                    <span>Vé của tôi</span>
                                </a>
                            </li>
                            @if($currentUser->isAdmin())
                            <li>
                                <a class="dropdown-item text-light py-2 px-3 small d-flex align-items-center gap-2" href="{{ url('/admin/dashboard') }}">
                                    <i class="fa-solid fa-gauge-high text-primary"></i>
                                    <span>Trang quản trị</span>
                                </a>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                            <li>
                                <form action="{{ url('/logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2 px-3 small d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('/login') }}" class="btn btn-wc-outline-light btn-sm px-3">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            <span>Đăng nhập</span>
                        </a>
                        <a href="{{ url('/register') }}" class="btn btn-wc-gold btn-sm px-3">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Đăng ký</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</header>
