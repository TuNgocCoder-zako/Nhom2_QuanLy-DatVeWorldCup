@extends('layouts.app')

@section('title', 'Xác Nhận Đặt Vé & Thanh Toán - World Cup 2030')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-credit-card text-warning me-2"></i> Xác Nhận Đơn Hàng & Thanh Toán</h2>
        <p class="text-secondary mb-0">Kiểm tra thông tin vé đã chọn và lựa chọn phương thức thanh toán</p>
    </div>

    <form action="{{ url('/booking/payment') }}" method="POST">
        @csrf
        <input type="hidden" name="match_id" value="{{ $match->id }}">
        @foreach($seatIds as $sid)
            <input type="hidden" name="seat_ids[]" value="{{ $sid }}">
        @endforeach

        <div class="row g-4">
            <!-- Left Column: Match & Seats Details -->
            <div class="col-lg-8">
                <!-- Match Information Card -->
                <div class="card shadow-sm border-0 mb-4 bg-white">
                    <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center" style="background: #0b132b !important;">
                        <span class="fw-bold text-warning"><i class="fa-solid fa-futbol me-2"></i> Thông Tin Trận Đấu</span>
                        <span class="badge bg-warning text-dark">Trận #{{ $match->match_number ?? $match->id }}</span>
                    </div>
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-dark mb-2">
                            {{ $match->homeTeam->name ?? 'Đội nhà' }} <span class="text-warning">VS</span> {{ $match->awayTeam->name ?? 'Đội khách' }}
                        </h4>
                        <div class="row text-secondary small g-2">
                            <div class="col-sm-6">
                                <i class="fa-solid fa-trophy text-warning me-1"></i> Giai đoạn: <strong>{{ $match->stage ?? 'Vòng bảng' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <i class="fa-regular fa-clock text-warning me-1"></i> Thời gian: <strong>{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i - d/m/Y') : 'Chưa định' }}</strong>
                            </div>
                            <div class="col-12">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i> Sân vận động: <strong>{{ $match->stadium->stadium_name ?? ($match->stadium->name ?? 'Sân vận động') }}</strong> ({{ $match->stadium->location ?? 'Tây Ban Nha' }})
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Seats Table -->
                <div class="card shadow-sm border-0 mb-4 bg-white">
                    <div class="card-header bg-light p-3">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-chair text-primary me-2"></i> Danh Sách Ghế Đã Chọn ({{ count($seats) }} vé)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Mã ghế</th>
                                        <th>Khu vực khán đài</th>
                                        <th>Loại vé</th>
                                        <th class="text-end pe-3">Giá vé</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seats as $seat)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">
                                            <span class="badge bg-warning text-dark fs-6">{{ $seat->seat_code }}</span>
                                        </td>
                                        <td>{{ $seat->seatZone->name ?? ($seat->seatZone->zone_name ?? 'Khán đài') }}</td>
                                        <td>
                                            <span class="badge {{ ($seat->seatZone->type ?? '') === 'VIP' ? 'bg-danger' : 'bg-primary' }}">
                                                {{ $seat->seatZone->type ?? 'STANDARD' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-danger">
                                            {{ number_format((float)$seat->price, 0, ',', '.') }} VNĐ
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="ps-3 fw-bold text-dark fs-6">Tổng cộng:</td>
                                        <td class="text-end pe-3 fw-bold text-danger fs-5">
                                            {{ number_format((float)$totalAmount, 0, ',', '.') }} VNĐ
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Card -->
                <div class="card shadow-sm border-0 mb-4 bg-white">
                    <div class="card-header bg-light p-3">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-wallet text-success me-2"></i> Chọn Phương Thức Thanh Toán</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card p-3 border rounded cursor-pointer h-100 d-flex flex-row align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="VNPAY" checked class="form-check-input mt-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-qrcode text-danger me-1"></i> VNPAY (QR Code)</div>
                                        <small class="text-muted">Quét mã VNPAY-QR qua ứng dụng ngân hàng</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="card p-3 border rounded cursor-pointer h-100 d-flex flex-row align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="MOMO" class="form-check-input mt-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-mobile-screen-button text-primary me-1"></i> Ví MoMo</div>
                                        <small class="text-muted">Thanh toán tức thì qua ví điện tử MoMo</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="card p-3 border rounded cursor-pointer h-100 d-flex flex-row align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="CREDIT_CARD" class="form-check-input mt-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="fa-regular fa-credit-card text-warning me-1"></i> Thẻ Quốc Tế</div>
                                        <small class="text-muted">Visa, MasterCard, JCB</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="card p-3 border rounded cursor-pointer h-100 d-flex flex-row align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="BANK_TRANSFER" class="form-check-input mt-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-building-columns text-info me-1"></i> Chuyển Khoản 24/7</div>
                                        <small class="text-muted">Chuyển khoản liên ngân hàng Napas247</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Confirmation Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 85px;">
                    <div class="card-header bg-dark text-white p-3" style="background: #0b132b !important;">
                        <span class="fw-bold text-warning"><i class="fa-solid fa-user-check me-2"></i> Người Đặt Vé</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Họ và tên</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Email nhận vé điện tử</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Số điện thoại liên hệ</label>
                            <input type="text" class="form-control" value="{{ $user->phone ?? 'Chưa cập nhật' }}" readonly>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Số lượng vé:</span>
                            <span class="fw-bold text-dark">{{ count($seats) }} vé</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold text-dark fs-6">Tổng thanh toán:</span>
                            <span class="fw-bold text-danger fs-5">{{ number_format((float)$totalAmount, 0, ',', '.') }} VNĐ</span>
                        </div>

                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="fa-solid fa-lock me-1"></i> Cổng thanh toán mô phỏng sandbox an toàn. Vé sẽ được xuất ngay sau khi xác nhận.
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-dark shadow-sm">
                            <i class="fa-solid fa-check-to-slot me-2"></i> Xác Nhận & Thanh Toán
                        </button>

                        <a href="{{ url('/matches/' . $match->id) }}" class="btn btn-outline-secondary w-100 btn-sm mt-2">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại chọn ghế khác
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
