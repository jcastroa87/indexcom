@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">Plan Optimization Analytics</span>
        </h2>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="alert alert-info">
                <i class="la la-info-circle"></i> This dashboard provides insights to help optimize your API subscription plans based on actual usage patterns.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    Usage Metrics by Plan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th class="text-right">Request Limit</th>
                                    <th class="text-right">Unique Users</th>
                                    <th class="text-right">Total Requests</th>
                                    <th class="text-right">Avg. Requests/User</th>
                                    <th class="text-right">Utilization</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageByPlan as $plan)
                                <tr>
                                    <td>{{ $plan->name }}</td>
                                    <td class="text-right">{{ number_format($plan->request_limit) }}</td>
                                    <td class="text-right">{{ number_format($plan->unique_users) }}</td>
                                    <td class="text-right">{{ number_format($plan->total_requests) }}</td>
                                    <td class="text-right">{{ number_format($plan->avg_requests_per_user, 1) }}</td>
                                    <td class="text-right">
                                        @php
                                            $utilization = ($plan->avg_requests_per_user / $plan->request_limit) * 100;
                                            $utilizationClass = 'bg-success';
                                            if ($utilization > 90) {
                                                $utilizationClass = 'bg-danger';
                                            } elseif ($utilization > 70) {
                                                $utilizationClass = 'bg-warning';
                                            } elseif ($utilization < 30) {
                                                $utilizationClass = 'bg-info';
                                            }
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $utilizationClass }}" role="progressbar" style="width: {{ min(100, $utilization) }}%" aria-valuenow="{{ $utilization }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($utilization, 1) }}%</div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Users Near Their Request Limit
                </div>
                <div class="card-body">
                    <canvas id="usersNearLimitChart" height="300"></canvas>
                </div>
                <div class="card-footer">
                    <div class="small text-muted">
                        <ul>
                            <li><strong>Critical</strong>: >90% of limit</li>
                            <li><strong>Warning</strong>: 70-90% of limit</li>
                            <li><strong>Comfortable</strong>: 50-70% of limit</li>
                            <li><strong>Low</strong>: <50% of limit</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Extended Data Usage
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($extendedDataUsage['total_requests']) }}</h3>
                                    <p class="mb-0">Total Extended Data Requests</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($extendedDataUsage['users_without_access_attempting']) }}</h3>
                                    <p class="mb-0">Users Attempting Without Access</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Most Requested Extended Endpoints</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Endpoint</th>
                                    <th class="text-right">Requests</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($extendedDataUsage['most_requested_endpoints'] as $endpoint => $count)
                                <tr>
                                    <td>{{ $endpoint }}</td>
                                    <td class="text-right">{{ number_format($count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    Plan Optimization Suggestions
                </div>
                <div class="card-body">
                    @foreach($planSuggestions as $suggestion)
                    <div class="alert alert-{{ $suggestion['type'] === 'new_tier' ? 'success' : ($suggestion['type'] === 'adjust_limit' ? 'warning' : 'info') }} mb-4">
                        <h5>{{ $suggestion['description'] }}</h5>
                        <p><strong>Rationale:</strong> {{ $suggestion['rationale'] }}</p>
                        <p><strong>Recommendation:</strong> {{ $suggestion['recommendation'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Users near limit chart
    const usersNearLimitCtx = document.getElementById('usersNearLimitChart').getContext('2d');
    const usersNearLimitChart = new Chart(usersNearLimitCtx, {
        type: 'pie',
        data: {
            labels: ['Critical (>90%)', 'Warning (70-90%)', 'Comfortable (50-70%)', 'Low (<50%)'],
            datasets: [{
                data: [
                    {{ $usersNearLimit['critical'] }},
                    {{ $usersNearLimit['warning'] - $usersNearLimit['critical'] }},
                    {{ $usersNearLimit['comfortable'] - $usersNearLimit['warning'] }},
                    {{ $usersNearLimit['low'] }}
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
