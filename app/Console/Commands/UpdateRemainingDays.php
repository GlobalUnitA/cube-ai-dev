<?php

namespace App\Console\Commands;

use App\Models\Staking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRemainingDays extends Command
{
    protected $signature = 'staking:update-remaining-days';
    protected $description = 'Update remaining_days for all stakings based on period and profits count';

    public function handle()
    {
        DB::beginTransaction();

        try {
            $stakings = Staking::with(['policy', 'rewards'])->get();

            foreach ($stakings as $staking) {
                $period = $staking->policy->period ?? 0;
                $profits_count = $staking->rewards->count();

                $staking->remaining_days = max($period - $profits_count, 0);
                $staking->save();

                Log::channel('staking')->info('Updated remaining_days', [
                    'staking_id' => $staking->id,
                    'user_id'    => $staking->user_id,
                    'period'     => $period,
                    'profits_count' => $profits_count,
                    'remaining_days' => $staking->remaining_days,
                    'timestamp'  => now(),
                ]);
            }

            DB::commit();
            $this->info('Remaining days updated successfully within a transaction.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('staking')->error('Failed to update remaining_days', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now(),
            ]);

            $this->error('Update failed. Transaction rolled back.');
        }
    }
}
