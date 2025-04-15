<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Index;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with index statistics.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get counts for key entities
        $stats = [
            'indices' => Index::count(),
            'active_indices' => Index::where('is_active', true)->count(),
            'rates' => Rate::count(),
            'users' => User::count(),
        ];

        // Get the latest rates for each index for the dashboard
        $latestRates = Index::where('is_active', true)
            ->with(['rates' => function ($query) {
                $query->latest('date')->limit(1);
            }])
            ->get()
            ->map(function ($index) {
                $latestRate = $index->rates->first();
                return [
                    'name' => $index->name,
                    'slug' => $index->slug,
                    'value' => $latestRate ? $latestRate->value : null,
                    'date' => $latestRate ? $latestRate->date->format('Y-m-d') : null,
                    'last_fetch' => $index->last_fetch_at ? Carbon::parse($index->last_fetch_at)->diffForHumans() : 'Never',
                ];
            });

        // Get rate count by date for the last 30 days for a chart
        $ratesByDate = Rate::select(DB::raw('DATE(date) as date'), DB::raw('count(*) as count'))
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Format chart data
        $chartLabels = json_encode(array_keys($ratesByDate));
        $chartValues = json_encode(array_values($ratesByDate));

        return view('vendor.backpack.ui.dashboard', compact(
            'stats',
            'latestRates',
            'chartLabels',
            'chartValues'
        ));
    }
}
