<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\MatchGame;
use App\Services\BookingService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('matches') && Schema::hasTable('match_seats') && Schema::hasTable('seat_zones')) {
            $service = app(BookingService::class);
            $matches = MatchGame::all();

            foreach ($matches as $match) {
                $count = \App\Models\MatchSeat::where('match_id', $match->id)->count();
                if ($count === 0) {
                    $service->generateSeatsForMatch($match);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Giữ an toàn dữ liệu ghế đã đặt
    }
};
