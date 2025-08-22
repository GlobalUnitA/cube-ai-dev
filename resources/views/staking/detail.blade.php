@extends('layouts.master')

@section('content')
<div class="container py-5">
    <h2 class="mb-3 text-center">{{ __('staking.staking') }} {{ __('staking.participation_list') }}</h2>
    <hr>
    @foreach($stakings as $staking)
    <div class="table-responsive overflow-x-auto pt-3">
        <table class="table table-striped table-bordered break-keep-all">
            <thead class="mb-2">
                <tr>
                    <th class="text-center" colspan="3">{{ $staking->policy->staking_locale_name }}</th>
                </tr>
                <tr>
                    <th>{{ __('system.date') }}</th>
                    <th>{{ __('staking.participation_quantity') }}</th>
                    <th>{{ __('asset.profit') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ date_format($staking->created_at, 'Y-m-d h:i:s') }}</td>
                    <td>{{ number_format($staking->amount) }}</td>
                    <td>{{ rtrim(rtrim(number_format($staking->getDailyProfit(), 9, '.', ''), '0'), '.') }}</td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center align-items-center w-100 mb-3">
            <a href="{{ route('staking.profit', ['id' => $staking->id]) }}">
                <h5 class="btn btn-outline-primary m-0">{{ __('asset.staking_profit') }}</h5>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection
