<?php

use App\Models\MatchGame;
use App\Services\BookingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('matches') || !Schema::hasTable('match_seats') || !Schema::hasTable('seat_zones')) {
            return;
        }

        $bookingService = app(BookingService::class);
        MatchGame::query()->chunkById(100, function ($matches) use ($bookingService): void {
            foreach ($matches as $match) {
                $bookingService->generateSeatsForMatch($match);
            }
        });
    }

    public function down(): void
    {
        // Keep generated seats and all booking history intact.
    }
};
