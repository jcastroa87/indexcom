<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of available plans.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('price')
            ->get(['id', 'name', 'slug', 'description', 'price', 'request_limit', 'has_extended_data', 'features', 'is_featured']);

        return response()->json([
            'data' => $plans,
            'message' => 'Available subscription plans'
        ]);
    }

    /**
     * Display the specified plan.
     *
     * @param string $id Plan ID or slug
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $plan = is_numeric($id)
            ? Plan::findOrFail($id)
            : Plan::where('slug', $id)->where('is_active', true)->firstOrFail();

        return response()->json([
            'data' => $plan,
            'message' => 'Plan details'
        ]);
    }
}
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
