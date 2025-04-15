@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">Subscription Analytics</span>
        </h2>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary mb-4">
                <div class="card-body">
                    <div class="card-title h5">Total Subscriptions</div>
                    <h2 class="mb-0">{{ number_format($totalSubscriptions) }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>All-time total</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success mb-4">
                <div class="card-body">
                    <div class="card-title h5">Active Subscriptions</div>
                    <h2 class="mb-0">{{ number_format($activeSubscriptions) }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Current active subscribers</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card text-white {{ $churnRate > 10 ? 'bg-danger' : 'bg-info' }} mb-4">
                <div class="card-body">
                    <div class="card-title h5">Churn Rate</div>
                    <h2 class="mb-0">{{ number_format($churnRate, 2) }}%</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Last 30 days</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-secondary mb-4">
                <div class="card-body">
                    <div class="card-title h5">Plan Changes</div>
                    <h2 class="mb-0">+{{ $planChanges['upgrades'] }} / -{{ $planChanges['downgrades'] }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Upgrades / Downgrades</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Active Subscriptions by Plan
                </div>
                <div class="card-body">
                    <canvas id="planCountsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Revenue by Plan
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Most Common Plan Upgrades
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <strong>{{ $planChanges['most_common_upgrade']['count'] }}</strong> users upgraded from
                        <strong>{{ $planChanges['most_common_upgrade']['from'] }}</strong> to
                        <strong>{{ $planChanges['most_common_upgrade']['to'] }}</strong>
                    </div>
                    <p>
                        This suggests that users find significant value in the features provided by the
                        {{ $planChanges['most_common_upgrade']['to'] }} plan compared to the
                        {{ $planChanges['most_common_upgrade']['from'] }} plan.
                    </p>
                    <p>
                        <strong>Recommendation:</strong> Consider highlighting the specific features that drive this upgrade
                        path in your marketing materials and on the subscription page.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Most Common Plan Downgrades
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>{{ $planChanges['most_common_downgrade']['count'] }}</strong> users downgraded from
                        <strong>{{ $planChanges['most_common_downgrade']['from'] }}</strong> to
                        <strong>{{ $planChanges['most_common_downgrade']['to'] }}</strong>
                    </div>
                    <p>
                        This suggests that some users may not be finding enough value in the higher-priced plan
                        to justify the cost difference.
                    </p>
                    <p>
                        <strong>Recommendation:</strong> Review the features and pricing structure of the
                        {{ $planChanges['most_common_downgrade']['from'] }} plan, and consider user feedback
                        to identify potential improvements.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    Plan Revenue Breakdown
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Subscribers</th>
                                    <th class="text-right">Monthly Revenue</th>
                                    <th class="text-right">% of Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalRevenue = $revenueByPlan->sum('revenue');
                                @endphp
                                @foreach($revenueByPlan as $plan)
                                <tr>
                                    <td>{{ $plan->name }}</td>
                                    <td class="text-right">${{ number_format($plan->price, 2) }}</td>
                                    <td class="text-right">{{ number_format($plan->count) }}</td>
                                    <td class="text-right">${{ number_format($plan->revenue, 2) }}</td>
                                    <td class="text-right">{{ number_format(($plan->revenue / $totalRevenue) * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                                <tr class="table-active font-weight-bold">
                                    <td>Total</td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($revenueByPlan->sum('count')) }}</td>
                                    <td class="text-right">${{ number_format($totalRevenue, 2) }}</td>
                                    <td class="text-right">100%</td>
                                </tr>
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
    // Plan counts chart
    const planCountsCtx = document.getElementById('planCountsChart').getContext('2d');
    const planCountsChart = new Chart(planCountsCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($planCounts->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($planCounts->pluck('count')) !!},
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

    // Revenue chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenueByPlan->pluck('name')) !!},
            datasets: [{
                label: 'Monthly Revenue ($)',
                data: {!! json_encode($revenueByPlan->pluck('revenue')) !!},
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
