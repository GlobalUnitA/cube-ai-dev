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
        Schema::table('staking_policies', function (Blueprint $table) {
            $table->enum('is_active', ['n', 'y'])->default('y')->after('coin_id');
            $table->string('staking_days', 50)->nullable()->default('mon,tue,wed,thu,fri,sat,sun')->after('period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staking_policies', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'staking_day']);
        });
    }
};
