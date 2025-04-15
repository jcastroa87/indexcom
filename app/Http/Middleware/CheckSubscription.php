<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'No active user found with this API key.'
            ], 401);
        }

        // Check if the user has an active subscription
        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return response()->json([
                'error' => 'Subscription Required',
                'message' => 'You need an active subscription to access this API endpoint.',
                'available_plans' => Plan::where('is_active', true)->select('id', 'name', 'price', 'request_limit')->get()
            ], 403);
        }

        // Check if the user has reached their request limit
        if ($subscription->hasReachedRequestLimit()) {
            return response()->json([
                'error' => 'Rate Limit Exceeded',
                'message' => 'You have reached your daily API request limit.',
                'limit' => $subscription->plan->request_limit,
                'usage' => $subscription->api_requests_today,
                'reset_at' => $subscription->api_requests_reset_date->toIso8601String(),
                'upgrade_plans' => Plan::where('is_active', true)
                    ->where('request_limit', '>', $subscription->plan->request_limit)
                    ->select('id', 'name', 'price', 'request_limit')
                    ->get()
            ], 429);
        }

        // Check if this is an extended data request and if the user has access
        $path = $request->path();
        if (strpos($path, '/extended') !== false && !$subscription->plan->has_extended_data) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'Your current plan does not include access to extended data.',
                'upgrade_plans' => Plan::where('is_active', true)
                    ->where('has_extended_data', true)
                    ->select('id', 'name', 'price')
                    ->get()
            ], 403);
        }

        // Increment the request counter
        $subscription->incrementRequestCount();

        return $next($request);
    }
}
