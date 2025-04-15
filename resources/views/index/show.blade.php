@extends('layouts.app')

@section('title', $index->name . ' Index')

@section('content')
<div class="container">
    <div class="row mt-4">
        <div class="col-12">
            <h1>{{ $index->name }}</h1>
            <p class="lead">{{ $index->description }}</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Today's Rate</h5>
                    @if($latestRate)
                        <div class="d-flex align-items-baseline">
                            <h2 class="display-1 me-2">{{ number_format($latestRate->value, 6) }}</h2>
                            <p class="text-muted">as of {{ $latestRate->date->format('M j, Y') }}</p>
                        </div>
                    @else
                        <p class="text-muted">No data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Historical Data</h5>
                    <p>Last 30 days evolution</p>

                    @if(count($historicalRates) > 0)
                        <canvas id="historyChart" height="200"></canvas>
                    @else
                        <p class="text-muted">No historical data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(count($historicalRates) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Historical Data Table</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historicalRates as $rate)
                                <tr>
                                    <td>{{ $rate->date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($rate->value, 6) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if(count($historicalRates) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('historyChart').getContext('2d');
        const labels = {!! $chartLabels !!};
        const values = {!! $chartValues !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ $index->name }} Index',
                    data: values,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
