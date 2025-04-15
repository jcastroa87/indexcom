@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">API Usage Analytics</span>
        </h2>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary mb-4">
                <div class="card-body">
                    <div class="card-title h5">Total Requests</div>
                    <h2 class="mb-0">{{ number_format($totalRequests) }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>{{ $range }} view</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success mb-4">
                <div class="card-body">
                    <div class="card-title h5">Unique Users</div>
                    <h2 class="mb-0">{{ number_format($uniqueUsers) }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Active API users</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-info mb-4">
                <div class="card-body">
                    <div class="card-title h5">Avg. Response Time</div>
                    <h2 class="mb-0">{{ number_format($averageResponseTime * 1000, 2) }} ms</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Across all endpoints</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card text-white {{ $errorRate > 5 ? 'bg-danger' : 'bg-secondary' }} mb-4">
                <div class="card-body">
                    <div class="card-title h5">Error Rate</div>
                    <h2 class="mb-0">{{ number_format($errorRate, 2) }}%</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>HTTP 4xx/5xx responses</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    Time Range
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="{{ backpack_url('analytics?range=day') }}" class="btn btn-outline-primary {{ $range === 'day' ? 'active' : '' }}">Day</a>
                        <a href="{{ backpack_url('analytics?range=week') }}" class="btn btn-outline-primary {{ $range === 'week' ? 'active' : '' }}">Week</a>
                        <a href="{{ backpack_url('analytics?range=month') }}" class="btn btn-outline-primary {{ $range === 'month' ? 'active' : '' }}">Month</a>
                        <a href="{{ backpack_url('analytics?range=quarter') }}" class="btn btn-outline-primary {{ $range === 'quarter' ? 'active' : '' }}">Quarter</a>
                        <a href="{{ backpack_url('analytics?range=year') }}" class="btn btn-outline-primary {{ $range === 'year' ? 'active' : '' }}">Year</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    Daily API Requests
                </div>
                <div class="card-body">
                    <canvas id="dailyUsageChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    Subscription Plan Distribution
                </div>
                <div class="card-body">
                    <canvas id="planDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Top API Endpoints
                </div>
                <div class="card-body">
                    <canvas id="endpointUsageChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Top API Users
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th class="text-right">Requests</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-right">{{ number_format($user->request_count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily usage chart
    const dailyUsageCtx = document.getElementById('dailyUsageChart').getContext('2d');
    const dailyUsageChart = new Chart(dailyUsageCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyUsage['labels']) !!},
            datasets: [{
                label: 'API Requests',
                data: {!! json_encode($dailyUsage['data']) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Plan distribution chart
    const planDistributionCtx = document.getElementById('planDistributionChart').getContext('2d');
    const planDistributionChart = new Chart(planDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($planDistribution['labels']) !!},
            datasets: [{
                data: {!! json_encode($planDistribution['data']) !!},
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Endpoint usage chart
    const endpointUsageCtx = document.getElementById('endpointUsageChart').getContext('2d');
    const endpointUsageChart = new Chart(endpointUsageCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($endpointUsage['labels']) !!},
            datasets: [{
                label: 'Request Count',
                data: {!! json_encode($endpointUsage['data']) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
