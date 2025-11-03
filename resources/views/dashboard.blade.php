@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-sm-flex align-items-baseline report-summary-header">
                                    <h5 class="font-weight-semibold pb-2">Report Summary</h5>
                                </div>
                            </div>
                        </div>
                        <div class="row report-inner-cards-wrapper">
                            @php
                                $colors = [
                                    'bg-success',
                                    'bg-danger',
                                    'bg-primary',
                                    'bg-info',
                                    'bg-secondary',
                                    'bg-dark',
                                ];
                                $icons = [
                                    'cards' => 'icon-credit-card',
                                    'shares' => 'icon-pie-chart',
                                    'properties' => 'icon-home',
                                    'improvements' => 'icon-settings',
                                    'car showrooms' => 'icon-speedometer',
                                    'aircraft shops' => 'icon-plane',
                                    'coins' => 'icon-wallet',
                                    'paintings' => 'icon-layers',
                                    'unique items' => 'icon-diamond',
                                    'retro cars' => 'icon-speedometer',
                                    'jewels' => 'icon-diamond',
                                    'stamps' => 'icon-note',
                                    'nfts' => 'icon-layers',
                                    'islands' => 'icon-globe-alt',
                                    'yacht shops' => 'icon-diamond',
                                    'cryptos' => 'icon-wallet',
                                    'insights' => 'icon-layers',
                                ];

                                $modulesChunks = collect($modules)->chunk(4); // ensures 4 per row
                            @endphp

                            @foreach ($modulesChunks as $chunk)
                                <div class="row mb-3">
                                    @foreach ($chunk as $module => $count)
                                        @php
                                            $iconKey = strtolower($module); // normalize for lookup
                                            $iconClass = $icons[$iconKey] ?? 'icon-layers';
                                        @endphp
                                        <div class="col-md-3 report-inner-card mb-3"> <!-- fixed 4 per row -->
                                            <div class="inner-card-text">
                                                <span class="report-title">{{ $module }}</span>
                                                <h4>{{ $count }}</h4>
                                                <span class="report-count">{{ $count }} Records</span>
                                            </div>
                                            <div class="inner-card-icon {{ $colors[array_rand($colors)] }}">
                                                <i class="{{ $iconClass }}"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
