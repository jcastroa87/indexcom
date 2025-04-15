<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Index;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Display the home page with a list of all active indices.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $indices = Index::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('web.index', compact('indices'));
    }

    /**
     * Display details for a specific index.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        $index = Index::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get the latest rate value
        $latestRate = $index->rates()
            ->latest('date')
            ->first();

        // Get historical data for the last 30 days
        $historicalData = $index->rates()
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->orderBy('date')
            ->get();

        // Format data for chart
        $chartLabels = $historicalData->pluck('date')->map(function ($date) {
            return $date->format('Y-m-d');
        })->toJson();

        $chartValues = $historicalData->pluck('value')->toJson();

        return view('web.show', compact(
            'index',
            'latestRate',
            'historicalData',
            'chartLabels',
            'chartValues'
        ));
    }

    /**
     * Display the API documentation.
     *
     * @return \Illuminate\View\View
     */
    public function apiDocs()
    {
        $apiDocPath = base_path('docs/api-documentation.md');
        $apiDocContent = '';

        if (file_exists($apiDocPath)) {
            $apiDocContent = file_get_contents($apiDocPath);
        }

        return view('web.api-docs', compact('apiDocContent'));
    }
}
