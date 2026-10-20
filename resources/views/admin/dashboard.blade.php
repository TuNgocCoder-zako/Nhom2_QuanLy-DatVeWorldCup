@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - FIFA World Cup 2030')
@section('page_title', 'Bảng Điều Khiển Thống Kê & Doanh Thu')

@section('content')
<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Doanh thu -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Tổng doanh thu</span>
                    <h4 class="fw-bold text-danger mb-0 mt-1">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h4>
                </div>
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-3">
                    <i class="fa-solid fa-coins fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Vé đã bán -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Vé đã bán ra</span>
                    <h4 class="fw-bold text-primary mb-0 mt-1">{{ number_format($totalTicketsSold) }} vé</h4>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="fa-solid fa-ticket fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Số trận đấu -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Số trận đấu</span>
                    <h4 class="fw-bold text-success mb-0 mt-1">{{ $totalMatches }} trận</h4>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                    <i class="fa-solid fa-futbol fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Người dùng -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold text-uppercase">Khách hàng</span>
                    <h4 class="fw-bold text-warning mb-0 mt-1">{{ $totalUsers }} user</h4>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3">
                    <i class="fa-solid fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart Section -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-chart-line text-primary me-2"></i> Biểu Đồ Tăng Trưởng Doanh Thu Bán Vé
        </h6>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary active" id="btnChartDaily">Theo ngày</button>
            <button type="button" class="btn btn-outline-primary" id="btnChartMonthly">Theo tháng</button>
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
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-receipt text-warning me-2"></i> Đơn Hàng Mới Nhất
                </h6>
                <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-link text-decoration-none">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3">Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-3 fw-bold">#{{ $order->order_code ?? $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Khách vãng lai' }}</td>
                                <td class="fw-bold text-danger">{{ number_format((float)($order->total_amount ?? $order->total_price), 0, ',', '.') }} đ</td>
                                <td>
                                    <span class="badge {{ $order->status === 'PAID' ? 'bg-success' : ($order->status === 'CANCELLED' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-sm btn-light border">
                                        <i class="fa-solid fa-eye"></i>
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
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-calendar-day text-success me-2"></i> Trận Đấu Sắp Tới
                </h6>
                <a href="{{ url('/admin/matches') }}" class="btn btn-sm btn-link text-decoration-none">Quản lý</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small">
                    @forelse($upcomingMatches as $match)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-warning text-dark">Trận #{{ $match->match_number ?? $match->id }}</span>
                            <span class="text-muted">{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : '' }}</span>
                        </div>
                        <div class="fw-bold text-dark">
                            {{ $match->homeTeam->name ?? 'Team A' }} vs {{ $match->awayTeam->name ?? 'Team B' }}
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
                            borderColor: '#f77f00',
                            backgroundColor: 'rgba(247, 127, 0, 0.15)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#0b132b',
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
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                    }
                                }
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
