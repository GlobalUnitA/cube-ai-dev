<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stakings', function (Blueprint $table) {
            $table->unsignedInteger('remaining_days')
                ->default(7)
                ->after('period')
                ->comment('남은 횟수');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stakings', function (Blueprint $table) {
            $table->dropColumn('remaining_days');
        });
    }
};
