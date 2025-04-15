<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard with API usage.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $subscription = $user->activeSubscription();
        $apiUsage = [];

        // Get current subscription info if exists
        if ($subscription) {
            // Get usage history (last 30 days)
            $apiUsage = $this->getApiUsageHistory($user->id);

            // Calculate usage trends
            $usageTrends = $this->calculateUsageTrends($apiUsage);

            return view('dashboard.index', [
                'user' => $user,
                'subscription' => $subscription,
                'apiUsage' => $apiUsage,
                'usageTrends' => $usageTrends,
                'availablePlans' => Plan::where('is_active', true)->get()
            ]);
        }

        // If no subscription, show available plans
        return view('dashboard.index', [
            'user' => $user,
            'subscription' => null,
            'apiUsage' => [],
            'usageTrends' => [],
            'availablePlans' => Plan::where('is_active', true)->get()
        ]);
    }

    /**
     * Show detailed API usage statistics.
     */
    public function apiUsage(Request $request)
    {
        $user = $request->user();
        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return redirect()->route('dashboard.index')
                ->with('error', 'You need an active subscription to view detailed API usage.');
        }

        // Get usage by endpoint
        $endpointUsage = $this->getEndpointUsage($user->id);

        // Get historical usage data for charts
        $dailyUsage = $this->getDailyUsage($user->id);
        $monthlyUsage = $this->getMonthlyUsage($user->id);

        return view('dashboard.api-usage', [
            'user' => $user,
            'subscription' => $subscription,
            'endpointUsage' => $endpointUsage,
            'dailyUsage' => $dailyUsage,
            'monthlyUsage' => $monthlyUsage
        ]);
    }

    /**
     * Show subscription management page.
     */
    public function subscriptions(Request $request)
    {
        $user = $request->user();
        $activeSubscription = $user->activeSubscription();
        $subscriptionHistory = $user->subscriptions()->orderBy('created_at', 'desc')->get();
        $availablePlans = Plan::where('is_active', true)->get();

        return view('dashboard.subscriptions', [
            'user' => $user,
            'activeSubscription' => $activeSubscription,
            'subscriptionHistory' => $subscriptionHistory,
            'availablePlans' => $availablePlans
        ]);
    }

    /**
     * Get API usage history for a user.
     */
    private function getApiUsageHistory($userId, $days = 30)
    {
        // This would normally fetch from a database table tracking API requests
        // For now, we'll return dummy data since we haven't implemented that tracking yet
        $usage = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $usage[] = [
                'date' => $date->format('Y-m-d'),
                'requests' => rand(10, 200), // Dummy data
                'errors' => rand(0, 20),     // Dummy data
            ];
        }

        return $usage;
    }

    /**
     * Calculate usage trends from historical data.
     */
    private function calculateUsageTrends($usageData)
    {
        // Simple calculation for demo purposes
        $totalRequests = array_sum(array_column($usageData, 'requests'));
        $totalDays = count($usageData);
        $avgRequests = $totalDays > 0 ? $totalRequests / $totalDays : 0;

        // Get usage for last 7 days vs previous 7 days
        $recentRequests = 0;
        $previousRequests = 0;

        for ($i = 0; $i < min(7, count($usageData)); $i++) {
            $recentRequests += $usageData[count($usageData) - 1 - $i]['requests'];
        }

        for ($i = 7; $i < min(14, count($usageData)); $i++) {
            $previousRequests += $usageData[count($usageData) - 1 - $i]['requests'];
        }

        $weeklyChange = $previousRequests > 0
            ? (($recentRequests - $previousRequests) / $previousRequests) * 100
            : 0;

        return [
            'daily_average' => round($avgRequests, 2),
            'weekly_change' => round($weeklyChange, 2),
            'total_requests' => $totalRequests,
        ];
    }

    /**
     * Get usage breakdown by endpoint.
     */
    private function getEndpointUsage($userId)
    {
        // Dummy data for endpoint usage
        return [
            ['endpoint' => '/api/indices', 'count' => 450, 'percentage' => 45],
            ['endpoint' => '/api/indices/{slug}/latest', 'count' => 320, 'percentage' => 32],
            ['endpoint' => '/api/indices/{slug}/rates', 'count' => 150, 'percentage' => 15],
            ['endpoint' => '/api/indices/{slug}/rates/extended', 'count' => 80, 'percentage' => 8],
        ];
    }

    /**
     * Get daily usage for chart visualization.
     */
    private function getDailyUsage($userId)
    {
        $usage = $this->getApiUsageHistory($userId, 14);

        return [
            'labels' => array_column($usage, 'date'),
            'data' => array_column($usage, 'requests'),
        ];
    }

    /**
     * Get monthly usage for chart visualization.
     */
    private function getMonthlyUsage($userId)
    {
        // Dummy data for monthly usage
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [1200, 1800, 2200, 1600, 2100, 2400],
        ];
    }
}
