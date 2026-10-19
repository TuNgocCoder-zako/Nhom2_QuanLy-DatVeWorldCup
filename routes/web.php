<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\StadiumController as AdminStadiumController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| Web & API Routes cho Hệ Thống Quản Lý & Đặt Vé FIFA World Cup 2030
| Đầy đủ 35 Endpoints theo đặc tả tài liệu tong_hop_api_world_cup_2030.docx
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 1. GIAO DIỆN NGƯỜI DÙNG & ĐẶT VÉ (USER WEB FLOW)
|--------------------------------------------------------------------------
*/
// STT 10: Xem trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// STT 11: Danh sách lịch đấu & lọc
Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');

// STT 12: Chi tiết trận & sơ đồ chọn ghế
Route::get('/matches/{id}', [MatchController::class, 'show'])->name('matches.show');

// STT 13: Chuyển ghế sang màn hình thanh toán
Route::post('/booking/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');

// STT 14: Xử lý thanh toán mô phỏng & chuyển hướng kết quả
Route::post('/booking/payment', [BookingController::class, 'processPayment'])->name('booking.payment');
Route::get('/booking/success', [BookingController::class, 'success'])->name('booking.success');

// STT 15: Danh sách vé đã mua của người dùng
Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my-tickets');

// STT 16: Tải vé điện tử định dạng PDF kèm mã QR
Route::get('/my-tickets/{id}/pdf', [TicketController::class, 'downloadPdf'])->name('tickets.download-pdf');

// STT 17: Hủy vé (khi đủ điều kiện)
Route::post('/my-tickets/{id}/cancel', [TicketController::class, 'cancelTicket'])->name('tickets.cancel');

/*
|--------------------------------------------------------------------------
| 2. XÁC THỰC TÀI KHOẢN (AUTHENTICATION WEB FLOW)
|--------------------------------------------------------------------------
*/
// STT 5 & 6: Đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// STT 7 & 8: Đăng ký tài khoản
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// STT 9: Đăng xuất tài khoản
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 3. PHÂN HỆ QUẢN TRỊ VIÊN (ADMIN DASHBOARD & CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // STT 18: Bảng điều khiển thống kê tổng quan
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // STT 19 - 22: Quản lý đội tuyển tham dự
    Route::get('/teams', [AdminTeamController::class, 'index'])->name('admin.teams.index');
    Route::get('/teams/create', [AdminTeamController::class, 'create'])->name('admin.teams.create');
    Route::get('/teams/{id}/edit', [AdminTeamController::class, 'edit'])->name('admin.teams.edit');
    Route::post('/teams', [AdminTeamController::class, 'store'])->name('admin.teams.store');
    Route::put('/teams/{id}', [AdminTeamController::class, 'update'])->name('admin.teams.update');
    Route::delete('/teams/{id}', [AdminTeamController::class, 'destroy'])->name('admin.teams.destroy');

    // STT 23 - 26: Quản lý sân vận động & khu ghế
    Route::get('/stadiums', [AdminStadiumController::class, 'index'])->name('admin.stadiums.index');
    Route::get('/stadiums/create', [AdminStadiumController::class, 'create'])->name('admin.stadiums.create');
    Route::get('/stadiums/{id}/edit', [AdminStadiumController::class, 'edit'])->name('admin.stadiums.edit');
    Route::post('/stadiums', [AdminStadiumController::class, 'store'])->name('admin.stadiums.store');
    Route::put('/stadiums/{id}', [AdminStadiumController::class, 'update'])->name('admin.stadiums.update');
    Route::delete('/stadiums/{id}', [AdminStadiumController::class, 'destroy'])->name('admin.stadiums.destroy');

    // STT 27 - 30: Quản lý lịch đấu & tự động sinh ghế
    Route::get('/matches', [AdminMatchController::class, 'index'])->name('admin.matches.index');
    Route::get('/matches/create', [AdminMatchController::class, 'create'])->name('admin.matches.create');
    Route::get('/matches/{id}/edit', [AdminMatchController::class, 'edit'])->name('admin.matches.edit');
    Route::post('/matches', [AdminMatchController::class, 'store'])->name('admin.matches.store');
    Route::put('/matches/{id}', [AdminMatchController::class, 'update'])->name('admin.matches.update');
    Route::delete('/matches/{id}', [AdminMatchController::class, 'destroy'])->name('admin.matches.destroy');

    // STT 31 - 33: Quản lý đơn hàng đặt vé & hủy đơn cấp quản trị
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');

    // STT 34 - 35: Quản lý người dùng & phân quyền tài khoản
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
});

/*
|--------------------------------------------------------------------------
| 4. REST JSON API (FETCH / AJAX / DI ĐỘNG / POSTMAN)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    // Auth APIs
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // STT 1: Lấy sơ đồ & trạng thái ghế
    Route::get('/matches/{id}/seats', [MatchController::class, 'getSeats']);

    // STT 2: Kiểm tra ghế trước thanh toán
    Route::post('/matches/{id}/check-seats', [BookingController::class, 'checkSeats']);
    Route::post('/check-seats', [BookingController::class, 'checkSeats']);

    // STT 3: Quét mã QR soát vé cổng
    Route::post('/tickets/{code}/verify', [TicketController::class, 'verify']);

    // STT 4: Dữ liệu biểu đồ doanh thu
    Route::get('/admin/dashboard/chart', [AdminDashboardController::class, 'chart']);

    // Đặt ghế & Tạo đơn hàng (REST API)
    Route::post('/matches/{id}/book-seats', [BookingController::class, 'bookSeats']);
    Route::post('/book-seats', [BookingController::class, 'bookSeats']);
    Route::post('/matches/{id}/hold-seats', [BookingController::class, 'holdSeats']);
    Route::post('/hold-seats', [BookingController::class, 'holdSeats']);

    // Payment lifecycle
    Route::post('/orders/{orderId}/payment', [PaymentController::class, 'create']);
    Route::post('/orders/{orderId}/payment/simulate', [PaymentController::class, 'simulate']);
    Route::post('/orders/{orderId}/payment/success', [PaymentController::class, 'success']);
    Route::post('/orders/{orderId}/payment/failure', [PaymentController::class, 'failure']);

    // Order operations
    Route::delete('/orders/{orderId}', [OrderController::class, 'cancel']);
    Route::get('/orders/{orderId}', [OrderController::class, 'show']);
    Route::get('/users/{userId}/orders', [OrderController::class, 'indexForUser']);

    // Ticket management & PDF stream
    Route::get('/users/{userId}/tickets', [TicketController::class, 'indexForUser']);
    Route::get('/tickets/{code}', [TicketController::class, 'show']);
    Route::get('/tickets/{id}/pdf', [TicketController::class, 'downloadPdf']);

    // Internal admin maintenance
    Route::post('/maintenance/release-expired-holds', [MaintenanceController::class, 'releaseExpiredHolds']);
});
