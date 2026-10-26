@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - FIFA World Cup 2030')
@section('page_title', 'Bảng Điều Khiển Thống Kê & Doanh Thu')

@section('content')
<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <!-- Doanh thu -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Tổng doanh thu</span>
                    <h3 class="font-display fw-bold text-danger mb-0 mt-2">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
                    <small class="text-success fw-medium mt-1 d-block"><i class="fa-solid fa-arrow-trend-up me-1"></i>Từ các đơn vé hợp lệ</small>
                </div>
                <div class="admin-stat-icon revenue shadow-sm">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Vé đã bán -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Vé đã phát hành</span>
                    <h3 class="font-display fw-bold text-primary mb-0 mt-2">{{ number_format($totalTicketsSold) }} <span class="fs-6 fw-normal text-muted">vé</span></h3>
                    <small class="text-primary fw-medium mt-1 d-block"><i class="fa-solid fa-qrcode me-1"></i>Mã định danh FIFA</small>
                </div>
                <div class="admin-stat-icon tickets shadow-sm">
                    <i class="fa-solid fa-ticket"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Số trận đấu -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Trận đấu tổ chức</span>
                    <h3 class="font-display fw-bold text-success mb-0 mt-2">{{ $totalMatches }} <span class="fs-6 fw-normal text-muted">trận</span></h3>
                    <small class="text-success fw-medium mt-1 d-block"><i class="fa-solid fa-landmark me-1"></i>Tại 16 thánh địa</small>
                </div>
                <div class="admin-stat-icon matches shadow-sm">
                    <i class="fa-solid fa-futbol"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Người dùng -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Tài khoản đăng ký</span>
                    <h3 class="font-display fw-bold text-warning mb-0 mt-2">{{ $totalUsers }} <span class="fs-6 fw-normal text-muted">user</span></h3>
                    <small class="text-secondary fw-medium mt-1 d-block"><i class="fa-solid fa-shield-halved me-1"></i>Khách hàng & Admin</small>
                </div>
                <div class="admin-stat-icon users shadow-sm">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart Section -->
<div class="admin-card mb-4">
    <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
        <div>
            <h5 class="font-display fw-bold mb-1 text-dark">
                <i class="fa-solid fa-chart-line text-warning me-2"></i> Tăng Trưởng Doanh Thu Bán Vé Trực Tuyến
            </h5>
            <small class="text-secondary">Dữ liệu doanh thu được tổng hợp thời gian thực theo từng ngày và từng tháng</small>
        </div>
        <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border" role="group">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 active" id="btnChartDaily">Theo ngày</button>
            <button type="button" class="btn btn-outline-primary rounded-pill px-3" id="btnChartMonthly">Theo tháng</button>
        </div>
    </div>
    <div class="card-body p-4">
        <div style="height: 320px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-7">
        <div class="admin-card h-100">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="font-display fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-receipt text-warning me-2"></i> Đơn Hàng Mới Nhất
                </h6>
                <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold">Xem tất cả &rarr;</a>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $order->order_code ?? $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Khách vãng lai' }}</td>
                                <td class="fw-bold text-danger">{{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} đ</td>
                                <td>
                                    @if($order->status === 'PAID')
                                        <span class="badge badge-pill-modern bg-success bg-opacity-10 text-success border border-success border-opacity-25">ĐÃ THANH TOÁN</span>
                                    @elseif($order->status === 'CANCELLED')
                                        <span class="badge badge-pill-modern bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">ĐÃ HỦY</span>
                                    @else
                                        <span class="badge badge-pill-modern bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-sm btn-light border btn-action-icon" title="Xem chi tiết đơn">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Chưa có đơn hàng nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Matches -->
    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="font-display fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-calendar-day text-success me-2"></i> Trận Đấu Sắp Tới
                </h6>
                <a href="{{ url('/admin/matches') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold">Quản lý &rarr;</a>
            </div>
            <div class="p-0">
                <div class="list-group list-group-flush small">
                    @forelse($upcomingMatches as $match)
                    <div class="list-group-item p-3 px-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-pill-modern bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25">Trận #{{ $match->match_number ?? $match->id }}</span>
                            <span class="text-muted"><i class="fa-regular fa-clock me-1"></i>{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : '' }}</span>
                        </div>
                        <div class="fw-bold text-dark font-display fs-6 my-1">
                            {{ $match->home_team_name }} vs {{ $match->away_team_name }}
                        </div>
                        <small class="text-secondary">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $match->stadium->stadium_name ?? $match->stadium->name }}
                        </small>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">Không có trận đấu sắp diễn ra.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    let chartInstance = null;

    function loadChartData(type) {
        fetch(`/api/admin/dashboard/chart?type=${type}`)
            .then(res => res.json())
            .then(data => {
                const labels = data.labels || [];
                const revenues = data.revenues || [];

                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh thu (VNĐ)',
                            data: revenues,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#070c1a',
                            pointBorderColor: '#f59e0b',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return new Intl.NumberFormat('vi-VN').format(context.raw) + ' VNĐ';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                    }
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
    }

    // Tải biểu đồ mặc định theo ngày
    loadChartData('daily');

    // Chuyển đổi qua lại giữa ngày và tháng
    document.getElementById('btnChartDaily').addEventListener('click', function () {
        this.classList.add('active');
        document.getElementById('btnChartMonthly').classList.remove('active');
        loadChartData('daily');
    });

    document.getElementById('btnChartMonthly').addEventListener('click', function () {
        this.classList.add('active');
        document.getElementById('btnChartDaily').classList.remove('active');
        loadChartData('monthly');
    });
});
</script>
@endsection
