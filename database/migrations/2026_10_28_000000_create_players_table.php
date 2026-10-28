<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('players')) {
            Schema::create('players', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('team_id');
                $table->string('name', 100);
                $table->integer('shirt_number')->nullable();
                $table->enum('position', ['GK', 'DF', 'MF', 'FW'])->default('MF');
                $table->timestamps();

                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            });
        }

        // Tự động nạp dữ liệu cầu thủ từ demo1.sql nếu bảng players đang rỗng
        if (Schema::hasTable('players') && DB::table('players')->count() === 0) {
            $sqlPath = base_path('demo1.sql');
            if (file_exists($sqlPath)) {
                $content = file_get_contents($sqlPath);
                if (preg_match('/INSERT\s+INTO\s+players\s*\(.*?\)\s*VALUES\s*(.*?);/is', $content, $matches)) {
                    $insertSql = "INSERT INTO players (team_id, name, shirt_number, position) VALUES " . $matches[1] . ";";
                    DB::unprepared($insertSql);
                }
            }
        }

        // Đồng bộ cập nhật Đội tuyển #4 thành Séc (CZE) nếu còn giữ bản ghi cũ
        if (Schema::hasTable('teams')) {
            DB::table('teams')->where('id', 4)->where('code', 'VIE')->update([
                'name'      => 'Séc',
                'team_name' => 'Séc',
                'code'      => 'CZE',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
