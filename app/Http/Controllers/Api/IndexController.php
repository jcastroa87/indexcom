<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Index;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * List all active indices.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $indices = Index::where('is_active', true)
            ->select(['id', 'name', 'slug', 'description'])
            ->get();

        return response()->json([
            'data' => $indices,
            'meta' => [
                'total' => $indices->count(),
                'timestamp' => Carbon::now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get details for a specific index.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $index = Index::where('slug', $slug)
            ->where('is_active', true)
            ->select(['id', 'name', 'slug', 'description'])
            ->firstOrFail();

        return response()->json([
            'data' => $index,
            'meta' => [
                'timestamp' => Carbon::now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get historical rates for a specific index.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function rates(Request $request, string $slug): JsonResponse
    {
        $index = Index::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = $index->rates();

        // Filter by date range if provided
        if ($request->has('from')) {
            $query->where('date', '>=', $request->get('from'));
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->get('to'));
        }

        // Limit if needed
        $limit = min((int)$request->get('limit', 30), 365);

        $rates = $query->latest('date')
            ->take($limit)
            ->get(['date', 'value']);

        return response()->json([
            'data' => $rates,
            'meta' => [
                'index' => $index->name,
                'slug' => $index->slug,
                'count' => $rates->count(),
                'timestamp' => Carbon::now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get the latest rate for a specific index.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function latest(string $slug): JsonResponse
    {
        $index = Index::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $latestRate = $index->rates()
            ->latest('date')
            ->first(['date', 'value']);

        if (!$latestRate) {
            return response()->json([
                'error' => 'No rates available for this index',
                'meta' => [
                    'index' => $index->name,
                    'slug' => $index->slug,
                    'timestamp' => Carbon::now()->toIso8601String(),
                ]
            ], 404);
        }

        return response()->json([
            'data' => $latestRate,
            'meta' => [
                'index' => $index->name,
                'slug' => $index->slug,
                'timestamp' => Carbon::now()->toIso8601String(),
            ]
        ]);
    }
}
