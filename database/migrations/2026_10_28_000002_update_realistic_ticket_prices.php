<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('seat_zones')) {
            // Cập nhật giá vé chuẩn thực tế World Cup: VIP = 8.500.000 VNĐ, STANDARD = 2.500.000 VNĐ
            DB::table('seat_zones')->where('type', 'VIP')->update(['base_price' => 8500000.00]);
            DB::table('seat_zones')->where('type', 'STANDARD')->update(['base_price' => 2500000.00]);
        }

        if (Schema::hasTable('match_seats') && Schema::hasTable('seat_zones')) {
            DB::table('match_seats')
                ->join('seat_zones', 'match_seats.seat_zone_id', '=', 'seat_zones.id')
                ->where('seat_zones.type', 'VIP')
                ->update(['match_seats.price' => 8500000.00]);

            DB::table('match_seats')
                ->join('seat_zones', 'match_seats.seat_zone_id', '=', 'seat_zones.id')
                ->where('seat_zones.type', 'STANDARD')
                ->update(['match_seats.price' => 2500000.00]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Giữ an toàn dữ liệu
    }
};
