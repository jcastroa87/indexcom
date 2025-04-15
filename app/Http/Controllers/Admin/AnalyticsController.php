<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display API usage analytics dashboard.
     */
    public function index(Request $request)
    {
        // Determine the time range for analytics
        $range = $request->input('range', 'week');
        $endDate = Carbon::now();
        $startDate = $this->getStartDateFromRange($range);

        // Get basic metrics
        $totalRequests = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])->count();
        $uniqueUsers = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count('user_id');
        $averageResponseTime = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])
            ->avg('response_time');
        $errorRate = $this->calculateErrorRate($startDate, $endDate);

        // Get usage data for charts
        $dailyUsage = $this->getDailyUsage($startDate, $endDate);
        $endpointUsage = $this->getEndpointUsage($startDate, $endDate);
        $planDistribution = $this->getPlanDistribution();
        $topUsers = $this->getTopUsers($startDate, $endDate);

        return view('admin.analytics.index', [
            'range' => $range,
            'totalRequests' => $totalRequests,
            'uniqueUsers' => $uniqueUsers,
            'averageResponseTime' => $averageResponseTime,
            'errorRate' => $errorRate,
            'dailyUsage' => $dailyUsage,
            'endpointUsage' => $endpointUsage,
            'planDistribution' => $planDistribution,
            'topUsers' => $topUsers,
        ]);
    }

    /**
     * Display subscription analytics.
     */
    public function subscriptions(Request $request)
    {
        // Get subscription metrics
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })->count();

        $planCounts = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->where(function ($query) {
                $query->whereNull('subscriptions.ends_at')
                    ->orWhere('subscriptions.ends_at', '>', now());
            })
            ->select('plans.name', DB::raw('count(*) as count'))
            ->groupBy('plans.name')
            ->get();

        $revenueByPlan = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->select('plans.name', 'plans.price', DB::raw('count(*) as count'),
                    DB::raw('(plans.price * count(*)) as revenue'))
            ->groupBy('plans.name', 'plans.price')
            ->orderBy('revenue', 'desc')
            ->get();

        // Calculate churn rate (subscriptions canceled in last 30 days / active at start of period)
        $thirtyDaysAgo = now()->subDays(30);
        $activeAtStart = Subscription::where('status', 'active')
            ->where('created_at', '<', $thirtyDaysAgo)
            ->where(function ($query) use ($thirtyDaysAgo) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', $thirtyDaysAgo);
            })->count();

        $canceledInPeriod = Subscription::where('status', 'canceled')
            ->whereBetween('updated_at', [$thirtyDaysAgo, now()])
            ->count();

        $churnRate = $activeAtStart > 0 ? ($canceledInPeriod / $activeAtStart) * 100 : 0;

        // Get plan upgrade/downgrade metrics
        $planChanges = $this->getPlanChanges();

        return view('admin.analytics.subscriptions', [
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'planCounts' => $planCounts,
            'revenueByPlan' => $revenueByPlan,
            'churnRate' => $churnRate,
            'planChanges' => $planChanges,
        ]);
    }

    /**
     * Display usage pattern analytics and suggestions for optimizing plans.
     */
    public function planOptimization(Request $request)
    {
        // Get average usage by plan
        $usageByPlan = DB::table('api_request_logs')
            ->join('subscriptions', 'api_request_logs.subscription_id', '=', 'subscriptions.id')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->select('plans.name', 'plans.request_limit',
                    DB::raw('COUNT(api_request_logs.id) as total_requests'),
                    DB::raw('COUNT(DISTINCT api_request_logs.user_id) as unique_users'),
                    DB::raw('COUNT(api_request_logs.id) / COUNT(DISTINCT api_request_logs.user_id) as avg_requests_per_user'))
            ->groupBy('plans.name', 'plans.request_limit')
            ->get();

        // Get percentage of users near their limit
        $usersNearLimit = $this->getUsersNearLimit();

        // Check for usage patterns indicating need for new plan tiers
        $planSuggestions = $this->getPlanSuggestions();

        // Extended data usage analysis
        $extendedDataUsage = $this->getExtendedDataUsage();

        return view('admin.analytics.plan-optimization', [
            'usageByPlan' => $usageByPlan,
            'usersNearLimit' => $usersNearLimit,
            'planSuggestions' => $planSuggestions,
            'extendedDataUsage' => $extendedDataUsage,
        ]);
    }

    /**
     * Get start date based on selected range.
     */
    private function getStartDateFromRange($range)
    {
        switch($range) {
            case 'day':
                return Carbon::now()->subDay();
            case 'week':
                return Carbon::now()->subWeek();
            case 'month':
                return Carbon::now()->subMonth();
            case 'quarter':
                return Carbon::now()->subQuarter();
            case 'year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subWeek();
        }
    }

    /**
     * Calculate error rate for API requests.
     */
    private function calculateErrorRate($startDate, $endDate)
    {
        $total = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])->count();
        $errors = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])
            ->where('response_code', '>=', 400)
            ->count();

        return $total > 0 ? ($errors / $total) * 100 : 0;
    }

    /**
     * Get daily usage data for chart.
     */
    private function getDailyUsage($startDate, $endDate)
    {
        $data = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get endpoint usage distribution.
     */
    private function getEndpointUsage($startDate, $endDate)
    {
        $data = ApiRequestLog::whereBetween('created_at', [$startDate, $endDate])
            ->select('endpoint', DB::raw('COUNT(*) as count'))
            ->groupBy('endpoint')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('endpoint')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get distribution of users across subscription plans.
     */
    private function getPlanDistribution()
    {
        $data = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->where(function ($query) {
                $query->whereNull('subscriptions.ends_at')
                    ->orWhere('subscriptions.ends_at', '>', now());
            })
            ->select('plans.name', DB::raw('COUNT(*) as count'))
            ->groupBy('plans.name')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get top users by API usage.
     */
    private function getTopUsers($startDate, $endDate)
    {
        return DB::table('api_request_logs')
            ->join('users', 'api_request_logs.user_id', '=', 'users.id')
            ->whereBetween('api_request_logs.created_at', [$startDate, $endDate])
            ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(*) as request_count'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('request_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get users who are close to their plan limits.
     */
    private function getUsersNearLimit()
    {
        $activeSubscriptions = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->get();

        $usersNearLimit = [];
        $thresholds = [
            'critical' => 0.9, // >90% of limit
            'warning' => 0.7,  // >70% of limit
            'comfortable' => 0.5, // >50% of limit
            'low' => 0, // <50% of limit
        ];

        foreach ($thresholds as $level => $threshold) {
            $count = 0;
            foreach ($activeSubscriptions as $subscription) {
                $usagePercentage = $subscription->api_requests_today / $subscription->plan->request_limit;
                if ($usagePercentage > $threshold) {
                    $count++;
                }
            }
            $usersNearLimit[$level] = $count;
        }

        return $usersNearLimit;
    }

    /**
     * Get plan change history (upgrades/downgrades).
     */
    private function getPlanChanges()
    {
        // In a real implementation, this would analyze subscription history
        // For demo purposes, we'll return sample data
        return [
            'upgrades' => 24,
            'downgrades' => 8,
            'most_common_upgrade' => [
                'from' => 'Basic',
                'to' => 'Professional',
                'count' => 15
            ],
            'most_common_downgrade' => [
                'from' => 'Enterprise',
                'to' => 'Professional',
                'count' => 5
            ]
        ];
    }

    /**
     * Get suggestions for optimizing plan tiers.
     */
    private function getPlanSuggestions()
    {
        // This would normally analyze usage patterns to suggest optimizations
        // For demo purposes, we'll return sample suggestions
        return [
            [
                'type' => 'new_tier',
                'description' => 'Consider adding a mid-tier plan between Basic and Professional',
                'rationale' => 'Many Basic users are hitting their limits but not upgrading to Professional',
                'recommendation' => 'New plan with 1000 requests/day at $49.99/month'
            ],
            [
                'type' => 'adjust_limit',
                'description' => 'Consider increasing the request limit for Professional plan',
                'rationale' => '65% of Professional users use >80% of their limit',
                'recommendation' => 'Increase from 2000 to 3000 requests/day'
            ],
            [
                'type' => 'feature_addition',
                'description' => 'Add extended data access to Basic plan as an optional add-on',
                'rationale' => '40% of Basic users have attempted to access extended data endpoints',
                'recommendation' => 'Optional add-on for $15/month'
            ]
        ];
    }

    /**
     * Analyze extended data usage.
     */
    private function getExtendedDataUsage()
    {
        // This would normally analyze extended data endpoint usage
        // For demo purposes, we'll return sample data
        return [
            'total_requests' => 5280,
            'users_with_access' => 128,
            'users_without_access_attempting' => 76,
            'most_requested_endpoints' => [
                '/api/indices/{slug}/rates/extended' => 3450,
                '/api/indices/{slug}/analysis' => 1280,
                '/api/indices/compare' => 550
            ]
        ];
    }
}
