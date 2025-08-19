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
        Schema::table('asset_transfers', function (Blueprint $table) {
            DB::statement("ALTER TABLE `asset_transfers` MODIFY COLUMN `type` ENUM('deposit','withdrawal','internal','manual_deposit', 'staking', 'staking_refund') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfers', function (Blueprint $table) {
            DB::statement("ALTER TABLE `asset_transfers` MODIFY COLUMN `type` ENUM('deposit','withdrawal','internal','manual_deposit', 'staking_refund') NOT NULL");
        });
    }
};
