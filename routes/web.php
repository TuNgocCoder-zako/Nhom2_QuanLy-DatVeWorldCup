<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web & API Routes cho Hệ Thống Đặt Vé World Cup 2030
|--------------------------------------------------------------------------
*/

// Nhóm các API trả về dữ liệu JSON (bắt đầu bằng tiền tố /api)
Route::prefix('api')->group(function () {
    // API 1: Lấy sơ đồ và trạng thái ghế theo mã trận đấu
    Route::get('/matches/{id}/seats', [MatchController::class, 'getSeats']);

    // API 2: Kiểm tra các ghế người dùng chọn có còn trống không trước khi thanh toán
    Route::post('/matches/{id}/check-seats', [BookingController::class, 'checkSeats']);
});
