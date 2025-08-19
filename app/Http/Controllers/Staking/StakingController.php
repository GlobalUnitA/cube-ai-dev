<?php

namespace App\Http\Controllers\Staking;

use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Income;
use App\Models\Staking;
use App\Models\StakingPolicy;
use App\Models\StakingReward;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;

class StakingController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $assets = Asset::where('user_id', auth()->id())
            ->whereHas('coin', function ($query) {
            $query->where('is_active', 'y');
        })
        ->get();

        return view('staking.staking', compact('assets'));
    }

    public function detail()
    {
        $stakings = Staking::where('user_id', auth()->id())->get();

        return view('staking.detail', compact('stakings'));
    }

    public function profit(Request $request)
    {
        $staking = Staking::find($request->id);
        $profits = StakingReward::where('staking_id', $staking->id)->get();

        return view('staking.profit', compact('staking', 'profits'));
    }

    public function confirm($id)
    {
        $staking = StakingPolicy::find($id);

        $asset = Asset::where('user_id', auth()->id())
            ->where('coin_id', $staking->coin_id)
            ->first();
        $balance = $asset->balance;

        $date = $this->getStakingDate($staking);

        return view('staking.confirm', compact('staking', 'date', 'balance'));
    }


    public function data(Request $request)
    {
        $staking = StakingPolicy::where('coin_id', $request->coin)
            ->where('is_active', 'y')
            ->get();

        return response()->json($staking->toArray());
    }
    public function store(Request $request)
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $staking = StakingPolicy::find($request->staking);

        $min = $staking->min_quantity;
        $max = $staking->max_quantity;

        if ($request->amount < $min || $request->amount > $max) {

            return response()->json([
                'status' => 'error',
                'message' =>  __('staking.participation_quantity_notice', ['min' => $min, 'max' => $max]),
            ]);
        }

        DB::beginTransaction();

        try {

            $asset = Asset::where('user_id', auth()->id())->where('coin_id', $staking->coin_id)->first();
            $income = Income::where('user_id', auth()->id())->where('coin_id', $staking->coin_id)->first();

            if ($asset->balance < $request->amount) {
                throw new \Exception(__('asset.lack_balance_notice'));
            }

            $staking = Staking::create([
                'user_id' => auth()->id(),
                'asset_id' => $asset->id,
                'income_id' => $income->id,
                'staking_id' => $staking->id,
                'amount' => $request->amount,
                'period' => $staking->period,
                'remaining_days' => $staking->period,
            ]);

            AssetTransfer::create([
                'user_id' => $staking->user_id,
                'asset_id' => $asset->id,
                'type' => 'staking',
                'status' => 'completed',
                'amount' => $request->amount,
                'actual_amount' => $request->amount,
                'before_balance' => $asset->balance,
                'after_balance' => $asset->balance - $request->amount,
            ]);

            $asset->update([
                'balance' => $asset->balance - $request->amount
            ]);

            $income->user->profile->referralBonus($staking);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('staking.staking_success_notice'),
                'url' => route('home'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' =>  $e->getMessage(),
            ]);

        }

    }

    private function getStakingDate($policy)
    {

        $start = Carbon::today()->addDays(1);

        $end = $start->copy();

        $dayMap = [
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        $available_days = array_map(fn($d) => $dayMap[strtolower($d)], explode(',', $policy->staking_days));

        $period = $policy->period;
        $count = 0;

        while ($count < $period) {
            if (in_array($end->dayOfWeek, $available_days)) {
                $count++;
            }

            if ($count < $period) {
                $end->addDay();
            }
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
