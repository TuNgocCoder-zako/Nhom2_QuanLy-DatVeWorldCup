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
        if (!Schema::hasTable('matches') || !Schema::hasTable('users')) {
            $sqlPath = base_path('demo1.sql');
            if (file_exists($sqlPath)) {
                $sql = file_get_contents($sqlPath);
                DB::unprepared($sql);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Giữ an toàn cho CSDL
    }
};
