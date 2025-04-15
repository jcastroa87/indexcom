<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start timing the request
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Only log API requests
        if (str_starts_with($request->path(), 'api/')) {
            // Calculate response time
            $responseTime = microtime(true) - $startTime;

            // Get authenticated user if available
            $user = Auth::user();

            // Create log entry
            ApiRequestLog::create([
                'user_id' => $user ? $user->id : null,
                'subscription_id' => $user && $user->activeSubscription() ? $user->activeSubscription()->id : null,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'response_code' => $response->getStatusCode(),
                'response_time' => $responseTime,
                'user_agent' => $request->userAgent(),
            ]);

            // Check for rate limit notifications
            if ($user && $user->activeSubscription()) {
                $subscription = $user->activeSubscription();
                $usagePercentage = ($subscription->api_requests_today / $subscription->plan->request_limit) * 100;

                // Send a warning notification when the user reaches 80% of their limit
                if ($usagePercentage >= 80 && $usagePercentage < 85) {
                    $subscription->notifyUser('limit_warning');
                }
            }
        }

        return $response;
    }
}
