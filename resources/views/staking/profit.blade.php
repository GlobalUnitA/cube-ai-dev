@extends('layouts.master')

@section('content')
<div class="container py-5">
    <h2 class="mb-3 text-center">{{ __('asset.staking_profit') }}</h2>
    <hr>
    <h5 class="text-center mb-2">{{ $staking->policy->staking_locale_name }}</h5>
    <div class="table-responsive overflow-x-auto pt-3">
        <table class="table table-striped table-bordered break-keep-all">
            <thead class="mb-2">
                <tr>
                    <th>{{ __('system.date') }}</th>
                    <th>{{ __('staking.participation_quantity') }}</th>
                    <th>{{ __('system.status') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ date_format($staking->created_at, 'Y-m-d h:i:s') }}</td>
                    <td>{{ number_format($staking->amount) }}</td>
                    <td>{{ $staking->status_text }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="table-responsive overflow-x-auto pt-3">
        <h5>{{ __('asset.profit_detail') }}</h5>
        <table class="table table-striped table-bordered break-keep-all">
            <thead class="mb-2">
                <tr>
                    <th>{{ __('system.date') }}</th>
                    <th>{{ __('asset.profit') }}</th>
                    <th>{{ __('system.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profits as $profit)
                    <tr>
                        <td>{{ date_format($profit->created_at, 'Y-m-d h:i:s') }}</td>
                        <td>{{ rtrim(rtrim(number_format($profit->profit, 9, '.', ''), '0'), '.') }}</td>
                        <td>{{ $profit->status_text }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No Data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
