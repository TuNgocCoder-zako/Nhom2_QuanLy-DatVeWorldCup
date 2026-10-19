<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web & API Routes cho Hệ Thống Đặt Vé World Cup 2030
|--------------------------------------------------------------------------
*/

// Nhóm các API trả về dữ liệu JSON (bắt đầu bằng tiền tố /api)
Route::prefix('api')->group(function () {
    // Manual session authentication for the XAMPP front controller.
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // API 1: Lấy sơ đồ và trạng thái ghế theo mã trận đấu
    Route::get('/matches/{id}/seats', [MatchController::class, 'getSeats']);

    // API 2: Kiểm tra các ghế người dùng chọn có còn trống không trước khi thanh toán
    Route::post('/matches/{id}/check-seats', [BookingController::class, 'checkSeats']);
    Route::post('/check-seats', [BookingController::class, 'checkSeats']);

    // API 3: Đặt ghế và tạo đơn hàng (hỗ trợ cả /book-seats và /hold-seats, có hoặc không có match_id trong URL)
    Route::post('/matches/{id}/book-seats', [BookingController::class, 'bookSeats']);
    Route::post('/book-seats', [BookingController::class, 'bookSeats']);
    Route::post('/matches/{id}/hold-seats', [BookingController::class, 'holdSeats']);
    Route::post('/hold-seats', [BookingController::class, 'holdSeats']);

    // API 4-7: Payment lifecycle.
    Route::post('/orders/{orderId}/payment', [PaymentController::class, 'create']);
    Route::post('/orders/{orderId}/payment/simulate', [PaymentController::class, 'simulate']);
    Route::post('/orders/{orderId}/payment/success', [PaymentController::class, 'success']);
    Route::post('/orders/{orderId}/payment/failure', [PaymentController::class, 'failure']);

    // API 8-10: Order operations and history.
    Route::delete('/orders/{orderId}', [OrderController::class, 'cancel']);
    Route::get('/orders/{orderId}', [OrderController::class, 'show']);
    Route::get('/users/{userId}/orders', [OrderController::class, 'indexForUser']);

    // API: Quản lý vé
    Route::get('/users/{userId}/tickets', [\App\Http\Controllers\TicketController::class, 'indexForUser']);
    Route::get('/tickets/{code}', [\App\Http\Controllers\TicketController::class, 'show']);

    // API 11: Internal admin maintenance.
    Route::post('/maintenance/release-expired-holds', [MaintenanceController::class, 'releaseExpiredHolds']);
});
