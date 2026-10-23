@extends('layouts.app')

@section('title', 'FIFA World Cup 2030 - Cổng Bán Vé Chính Thức | Tây Ban Nha, Bồ Đào Nha & Ma-rốc')

@section('content')
<!-- ========================================================================
     HERO SECTION (Centenary 1930 - 2030)
     ======================================================================== -->
<section class="wc-hero text-center">
    <div class="container position-relative">
        <!-- Centenary Eyebrow Pill -->
        <div class="d-inline-flex align-items-center mb-3">
            <span class="wc-hero-badge">
                <i class="fa-solid fa-trophy text-gold"></i>
                <span>100 Năm Lịch Sử World Cup (1930 &bull; 2030)</span>
            </span>
        </div>

        <!-- Main Headline -->
        <h1 class="wc-hero-title">
            FIFA WORLD CUP <span class="text-gold">2030</span>
        </h1>
        <p class="wc-hero-sub">
            Chào mừng bạn đến với cổng đặt vé trực tuyến chính thức giải vô địch bóng đá thế giới FIFA World Cup 2030 tổ chức tại <strong>Tây Ban Nha, Bồ Đào Nha và Ma-rốc</strong>. Lựa chọn chỗ ngồi trên sơ đồ sân vận động 3D, thanh toán tức thì và nhận vé QR điện tử.
        </p>

        <!-- Live Tournament Countdown -->
        <div class="wc-countdown-grid" id="wcCountdown" aria-label="Đếm ngược đến ngày khai mạc FIFA World Cup 2030">
            <div class="wc-countdown-box">
                <div class="wc-countdown-num" id="countdownDays">--</div>
                <div class="wc-countdown-label">Ngày</div>
            </div>
            <div class="wc-countdown-box">
                <div class="wc-countdown-num" id="countdownHours">--</div>
                <div class="wc-countdown-label">Giờ</div>
            </div>
            <div class="wc-countdown-box">
                <div class="wc-countdown-num" id="countdownMins">--</div>
                <div class="wc-countdown-label">Phút</div>
            </div>
            <div class="wc-countdown-box">
                <div class="wc-countdown-num" id="countdownSecs">--</div>
                <div class="wc-countdown-label">Giây</div>
            </div>
        </div>

        <!-- CTA Action Buttons -->
        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
            <a href="{{ url('/matches') }}" class="btn btn-wc-gold btn-lg px-4 shadow">
                <i class="fa-solid fa-ticket"></i>
                <span>Đặt Vé Ngay</span>
            </a>
            <a href="#groups-section" class="btn btn-wc-outline-light btn-lg px-4">
                <i class="fa-solid fa-list-ol"></i>
                <span>Xem 12 Bảng Đấu</span>
            </a>
        </div>

        <!-- Quick Match Search Dock -->
        <div class="wc-hero-search-card text-start">
            <form action="{{ url('/matches') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6 wc-search-input-group">
                    <label for="heroDate"><i class="fa-regular fa-calendar me-1 text-warning"></i> Ngày thi đấu</label>
                    <input type="date" id="heroDate" name="date" class="form-control" value="{{ request('date') }}">
                </div>

                <div class="col-lg-3 col-md-6 wc-search-input-group">
                    <label for="heroTeam"><i class="fa-solid fa-flag me-1 text-warning"></i> Đội tuyển</label>
                    <select id="heroTeam" name="team" class="form-select">
                        <option value="">-- Tất cả đội tuyển --</option>
                        @if(isset($allTeams))
                            @foreach($allTeams as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-lg-4 col-md-8 wc-search-input-group">
                    <label for="heroStadium"><i class="fa-solid fa-landmark me-1 text-warning"></i> Sân vận động</label>
                    <select id="heroStadium" name="stadium" class="form-select">
                        <option value="">-- Tất cả sân vận động --</option>
                        @if(isset($allStadiums))
                            @foreach($allStadiums as $s)
                                <option value="{{ $s->id }}">{{ $s->stadium_name ?? $s->name }} ({{ $s->location }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <button type="submit" class="btn btn-wc-gold w-100 py-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Tìm Trận</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ========================================================================
     TOURNAMENT METRICS BAR
     ======================================================================== -->
<section class="wc-metrics-bar">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="wc-metric-item">
                    <div class="wc-metric-value text-gold">48</div>
                    <div class="wc-metric-label">Đội tuyển quốc gia</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wc-metric-item">
                    <div class="wc-metric-value text-gold">104</div>
                    <div class="wc-metric-label">Trận cầu đỉnh cao</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wc-metric-item">
                    <div class="wc-metric-value text-gold">16</div>
                    <div class="wc-metric-label">Thánh địa đăng cai</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="wc-metric-item">
                    <div class="wc-metric-value text-gold">100 Năm</div>
                    <div class="wc-metric-label">Lịch sử huy hoàng (1930 - 2030)</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================================
     HOT MATCHES SECTION (Tâm điểm giải đấu)
     ======================================================================== -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-3">
        <!-- Section Heading -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
            <div>
                <span class="badge bg-danger text-uppercase fw-bold px-3 py-1 mb-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                    <i class="fa-solid fa-fire me-1"></i> Trận Cầu Nổi Bật
                </span>
                <h2 class="font-display fw-bold text-dark mb-0 fs-2">Các Trận Đấu Đỉnh Cao Sắp Khởi Tranh</h2>
            </div>
            <a href="{{ url('/matches') }}" class="btn btn-outline-dark btn-sm fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2">
                <span>Xem tất cả lịch thi đấu</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- Hot Match Cards Grid -->
        <div class="row g-4">
            @forelse($hotMatches as $match)
            <div class="col-lg-6 col-md-12">
                <div class="wc-match-card h-100">
                    <!-- Card Top Broadcast Header -->
                    <div class="wc-match-header">
                        <span class="wc-match-badge-stage">
                            <i class="fa-solid fa-trophy me-1"></i> {{ $match->stage ?? 'Vòng bảng' }}
                        </span>
                        <div class="wc-match-time-tag">
                            <i class="fa-regular fa-calendar-check text-warning"></i>
                            <span>{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định ngày' }}</span>
                        </div>
                    </div>

                    <!-- Card Center Teams Arena -->
                    <div class="wc-match-body">
                        <div class="row align-items-center">
                            <!-- Đội nhà -->
                            <div class="col-5 wc-team-col">
                                <img src="{{ $match->homeTeam->flag_url }}" alt="{{ $match->homeTeam->name }}" class="wc-flag-shield" loading="lazy">
                                <div class="wc-team-title">{{ $match->homeTeam->name ?? 'Đội nhà' }}</div>
                                <span class="wc-code-pill">{{ $match->homeTeam->code ?? 'HOM' }}</span>
                            </div>

                            <!-- VS / Điểm số trung tâm -->
                            <div class="col-2 wc-vs-box">
                                @if($match->status === 'LIVE' || $match->status === 'FINISHED')
                                    <div class="wc-score-badge">
                                        {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                    </div>
                                    <span class="badge {{ $match->status === 'LIVE' ? 'bg-danger' : 'bg-secondary' }} small">
                                        {{ $match->status }}
                                    </span>
                                @else
                                    <span class="wc-vs-badge">VS</span>
                                @endif
                            </div>

                            <!-- Đội khách -->
                            <div class="col-5 wc-team-col">
                                <img src="{{ $match->awayTeam->flag_url }}" alt="{{ $match->awayTeam->name }}" class="wc-flag-shield" loading="lazy">
                                <div class="wc-team-title">{{ $match->awayTeam->name ?? 'Đội khách' }}</div>
                                <span class="wc-code-pill">{{ $match->awayTeam->code ?? 'AWA' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Bottom Details & CTA -->
                    <div class="wc-match-footer">
                        <div class="wc-match-venue">
                            <i class="fa-solid fa-location-dot text-danger"></i>
                            <span class="fw-medium text-dark text-truncate" style="max-width: 220px;" title="{{ $match->stadium->stadium_name ?? $match->stadium->name }}">
                                {{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="wc-seats-avail">
                                <i class="fa-solid fa-chair"></i>
                                <span>{{ $match->available_seats_count }} chỗ</span>
                            </span>
                            <a href="{{ url('/matches/' . $match->id) }}" class="btn btn-wc-gold btn-sm px-3">
                                <i class="fa-solid fa-ticket"></i>
                                <span>Chọn Ghế</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-4">Đang cập nhật danh sách các trận đấu sắp diễn ra.</div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- ========================================================================
     12 TOURNAMENT GROUPS SECTION (A -> L)
     ======================================================================== -->
<section class="py-5" id="groups-section" style="background: #ffffff; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container py-3">
        <!-- Section Header -->
        <div class="text-center mb-5">
            <span class="badge bg-primary text-uppercase px-3 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                Thể Thức Kỷ Lục
            </span>
            <h2 class="font-display fw-bold text-dark mt-2 fs-2">12 Bảng Đấu Tranh Tài FIFA World Cup 2030</h2>
            <p class="text-secondary mx-auto" style="max-width: 620px;">
                48 đội tuyển xuất sắc nhất toàn cầu được chia thành 12 bảng đấu (A đến L). Khám phá các hạt giống tranh vé vào vòng knock-out.
            </p>
        </div>

        <!-- Groups Cards Grid -->
        <div class="row g-4">
            @foreach($teamsByGroup as $groupName => $teams)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="wc-group-card h-100">
                    <div class="wc-group-header">
                        <span class="wc-group-name">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>BẢNG {{ $groupName }}</span>
                        </span>
                        <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.72rem;">{{ count($teams) }} ĐỘI</span>
                    </div>

                    <div class="wc-group-list">
                        @foreach($teams as $team)
                        <div class="wc-team-row">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $team->flag_url }}" alt="{{ $team->name }}" class="wc-mini-flag" loading="lazy">
                                <span class="fw-semibold text-dark">{{ $team->name }}</span>
                            </div>
                            <span class="wc-code-pill">{{ $team->code }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================================================
     FEATURED STADIUMS SHOWCASE (Thánh địa bóng đá)
     ======================================================================== -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                <i class="fa-solid fa-landmark me-1"></i> Thánh Địa Túc Cầu
            </span>
            <h2 class="font-display fw-bold text-dark mt-2 fs-2">Các Sân Vận Động Đăng Cai World Cup 2030</h2>
            <p class="text-secondary mx-auto" style="max-width: 600px;">
                Chiêm ngưỡng các sân vận động tráng lệ bậc nhất thế giới tại Tây Ban Nha, Bồ Đào Nha và Ma-rốc với sức chứa lên tới 100.000 khán giả.
            </p>
        </div>

        <div class="row g-4">
            @foreach($featuredStadiums as $stadium)
            <div class="col-lg-3 col-md-6">
                <div class="wc-stadium-card">
                    <div class="wc-stadium-img-wrapper">
                        <img src="{{ $stadium->image_url }}" alt="{{ $stadium->name }}" class="wc-stadium-img" loading="lazy">
                        <div class="wc-stadium-overlay">
                            <span class="badge bg-dark bg-opacity-75 text-white border border-secondary border-opacity-50">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $stadium->location }}
                            </span>
                            <span class="wc-stadium-cap-badge">
                                {{ number_format((int)$stadium->capacity) }} chỗ
                            </span>
                        </div>
                    </div>
                    <div class="wc-stadium-content">
                        <h3 class="wc-stadium-title">{{ $stadium->stadium_name ?? $stadium->name }}</h3>
                        <p class="wc-stadium-loc">
                            <i class="fa-solid fa-city me-1 text-secondary"></i> Thành phố đăng cai chính thức
                        </p>
                        <div class="mt-auto pt-2 border-top border-light d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Trạng thái: <strong>Sẵn sàng</strong></span>
                            <a href="{{ url('/matches?stadium=' . $stadium->id) }}" class="btn btn-sm btn-outline-dark fw-semibold" style="font-size: 0.78rem;">
                                Xem trận <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================================================
     SERVICE COMMITMENTS & VALUE PROPOSITIONS
     ======================================================================== -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="wc-prop-card">
                    <div class="wc-prop-icon">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <h4 class="fw-bold fs-5 text-dark mb-2">100% Vé Chính Hãng</h4>
                    <p class="text-secondary small mb-0">Hệ thống phân phối vé chính thức có định danh FIFA, cam kết không vé giả hoặc trùng số ghế.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="wc-prop-card">
                    <div class="wc-prop-icon">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h4 class="fw-bold fs-5 text-dark mb-2">Chọn Ghế Trực Quan</h4>
                    <p class="text-secondary small mb-0">Sơ đồ khán đài tương tác trực quan theo từng khu vực VIP, Standard giúp chọn chỗ ngồi ưng ý nhất.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="wc-prop-card">
                    <div class="wc-prop-icon">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <h4 class="fw-bold fs-5 text-dark mb-2">Thanh Toán Đa Dạng</h4>
                    <p class="text-secondary small mb-0">Hỗ trợ các cổng thanh toán ngân hàng, ví điện tử và thẻ quốc tế bảo mật theo chuẩn SSL 256-Bit.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="wc-prop-card">
                    <div class="wc-prop-icon">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h4 class="fw-bold fs-5 text-dark mb-2">Vé Điện Tử QR Code</h4>
                    <p class="text-secondary small mb-0">Vé được gửi tức thì về email và tải file PDF với mã QR cá nhân hóa tiện lợi khi vào sân.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Dynamic Centenary World Cup 2030 Countdown Timer (Target: June 8, 2030)
(function() {
    const targetDate = new Date('2030-06-08T18:00:00Z').getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const diff = targetDate - now;

        if (diff <= 0) {
            document.getElementById('countdownDays').innerText = '00';
            document.getElementById('countdownHours').innerText = '00';
            document.getElementById('countdownMins').innerText = '00';
            document.getElementById('countdownSecs').innerText = '00';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);

        const elDays = document.getElementById('countdownDays');
        const elHours = document.getElementById('countdownHours');
        const elMins = document.getElementById('countdownMins');
        const elSecs = document.getElementById('countdownSecs');

        if (elDays) elDays.innerText = days;
        if (elHours) elHours.innerText = hours < 10 ? '0' + hours : hours;
        if (elMins) elMins.innerText = mins < 10 ? '0' + mins : mins;
        if (elSecs) elSecs.innerText = secs < 10 ? '0' + secs : secs;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();
</script>
@endsection
