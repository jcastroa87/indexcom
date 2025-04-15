@extends('layouts.app')

@section('title', 'IndexCom - Exchange Indices')
@section('meta_description', 'Track daily exchange indices and visualize their progress through beautiful graphs')
@section('page-title', 'Exchange Indices')

@section('content')
<div class="row row-cards">
    @foreach($indices as $index)
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">{{ $index->name }}</h3>
                <p class="text-muted">{{ $index->description ?? 'Daily exchange rate index' }}</p>

                @php
                    $latestRate = $index->rates()->latest('date')->first();
                @endphp

                @if($latestRate)
                <div class="mt-3 mb-4">
                    <h2 class="mb-0">{{ number_format($latestRate->value, 6) }}</h2>
                    <div class="text-muted">
                        Last updated: {{ $latestRate->date->format('Y-m-d') }}
                    </div>
                </div>
                @else
                <div class="mt-3 mb-4">
                    <span class="text-muted">No data available</span>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex">
                    <a href="{{ route('index.show', $index->slug) }}" class="btn btn-primary ms-auto">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($indices->isEmpty())
    <div class="col-12">
        <div class="empty">
            <div class="empty-icon">
                <i class="ti ti-chart-bar"></i>
            </div>
            <p class="empty-title">No indices available</p>
            <p class="empty-subtitle text-muted">
                There are no active indices configured in the system.
            </p>
        </div>
    </div>
    @endif
</div>
@endsection
