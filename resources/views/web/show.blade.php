@extends('layouts.app')

@section('title', "{$index->name} - IndexCom")
@section('meta_description', "Track the daily rate and historical data for {$index->name}")
@section('page-title', $index->name)

@section('content')
<div class="row">
    <!-- Latest rate display -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="card-title mb-3">Current Rate</h3>
                @if($latestRate)
                <div class="display-6 fw-bold mb-1">{{ number_format($latestRate->value, 6) }}</div>
                <div class="text-muted">
                    As of {{ $latestRate->date->format('Y-m-d') }}
                </div>
                @else
                <div class="text-muted">No data available</div>
                @endif
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <h3 class="card-title">About this index</h3>
                <p>{{ $index->description ?? 'Daily exchange rate index.' }}</p>
                <div class="mt-3">
                    <div class="text-muted mb-1">Data Source:</div>
                    <div>{{ $index->source_api_url ? 'External API' : 'Manual entry' }}</div>
                    <div class="text-muted mt-3 mb-1">Update Frequency:</div>
                    <div>Every {{ $index->fetch_frequency }} minutes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historical chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Historical Data (30 Days)</h3>
                @if(count($historicalData) > 1)
                <div style="height: 300px;">
                    <canvas id="historicalChart"></canvas>
                </div>
                @else
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-chart-line"></i>
                    </div>
                    <p class="empty-title">No historical data</p>
                    <p class="empty-subtitle text-muted">
                        There isn't enough historical data to display a chart yet.
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Data table -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Data Table</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Value</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historicalData as $rate)
                        <tr>
                            <td>{{ $rate->date->format('Y-m-d') }}</td>
                            <td class="fw-bold">{{ number_format($rate->value, 6) }}</td>
                            <td>{{ $rate->is_manual ? 'Manual entry' : 'Automatic' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(count($historicalData) > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('historicalChart').getContext('2d');
    const labels = {!! $chartLabels !!};
    const values = {!! $chartValues !!};

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '{{ $index->name }} Rate',
                data: values,
                borderColor: '#206bc4',
                backgroundColor: 'rgba(32, 107, 196, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `Value: ${Number(context.raw).toFixed(6)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: false
                }
            }
        }
    });
});
</script>
@endif
@endsection
