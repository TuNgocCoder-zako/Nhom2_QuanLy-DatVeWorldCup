<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!Schema::hasTable('matches') || !Schema::hasTable('users')) {
            $sqlPath = base_path('demo1.sql');
            if (file_exists($sqlPath)) {
                $sql = file_get_contents($sqlPath);
                DB::unprepared($sql);
            }
        }
    }
}
