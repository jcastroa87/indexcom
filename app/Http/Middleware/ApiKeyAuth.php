<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if API key is provided in the request
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is missing',
                'message' => 'Please provide your API key in the X-API-Key header or as an api_key query parameter',
            ], 401);
        }

        // Find the user with this API key
        $user = User::where('api_key', $apiKey)
                    ->where('is_active', true)
                    ->first();

        if (!$user) {
            return response()->json([
                'error' => 'Invalid or inactive API key',
                'message' => 'The provided API key is invalid or has been deactivated',
            ], 401);
        }

        // Set the authenticated user for this request
        auth()->setUser($user);

        return $next($request);
    }
}
