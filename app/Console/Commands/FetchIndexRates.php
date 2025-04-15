<?php

namespace App\Console\Commands;

use App\Models\Index;
use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchIndexRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexcom:fetch-rates {--index=} {--force} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest rates for active indices from their respective APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $indexSlug = $this->option('index');
        $force = $this->option('force');
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();

        $query = Index::where('is_active', true);

        // If an index slug is provided, only fetch data for that index
        if ($indexSlug) {
            $query->where('slug', $indexSlug);
            $this->info("Fetching data for index: {$indexSlug}");
        } else {
            $this->info("Fetching data for all active indices");
        }

        $indices = $query->get();

        if ($indices->isEmpty()) {
            $this->error("No active indices found" . ($indexSlug ? " with slug {$indexSlug}" : ""));
            return 1;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($indices as $index) {
            try {
                // Skip if the frequency setting hasn't elapsed since last fetch
                // (unless force option is used)
                if (!$force && $index->last_fetch_at) {
                    $nextFetchTime = Carbon::parse($index->last_fetch_at)
                        ->addMinutes($index->fetch_frequency);

                    if (Carbon::now()->lt($nextFetchTime)) {
                        $this->info("Skipping {$index->name}: Next fetch scheduled at {$nextFetchTime->format('Y-m-d H:i:s')}");
                        continue;
                    }
                }

                // Check if we already have a rate for this date
                $existingRate = $index->rates()
                    ->whereDate('date', $date)
                    ->exists();

                if ($existingRate && !$force) {
                    $this->info("Skipping {$index->name}: Rate for {$date->format('Y-m-d')} already exists");
                    continue;
                }

                // Fetch the rate from the external API
                $this->info("Fetching {$index->name} rate for {$date->format('Y-m-d')}...");

                if (empty($index->source_api_url)) {
                    $this->warn("No API source configured for {$index->name}");
                    $errorCount++;
                    continue;
                }

                // Make the API request
                $headers = [];
                if (!empty($index->source_api_key)) {
                    $headers['Authorization'] = "Bearer {$index->source_api_key}";
                }

                $response = Http::withHeaders($headers)
                    ->get($index->source_api_url, [
                        'date' => $date->format('Y-m-d'),
                    ]);

                if (!$response->successful()) {
                    $this->error("Failed to fetch {$index->name}: HTTP status {$response->status()}");
                    Log::error("Failed to fetch {$index->name}", [
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                    $errorCount++;
                    continue;
                }

                // Parse the response
                $data = $response->json();
                $value = $this->extractValueFromResponse($data, $index->source_api_path);

                if ($value === null) {
                    $this->error("Failed to extract value from response for {$index->name}");
                    Log::error("Failed to extract value from response", [
                        'index' => $index->name,
                        'data' => $data,
                        'path' => $index->source_api_path,
                    ]);
                    $errorCount++;
                    continue;
                }

                // Save or update the rate
                $rate = Rate::updateOrCreate(
                    [
                        'index_id' => $index->id,
                        'date' => $date,
                    ],
                    [
                        'value' => $value,
                        'is_manual' => false,
                    ]
                );

                // Update the last fetch timestamp
                $index->last_fetch_at = Carbon::now();
                $index->save();

                $this->info("Successfully saved {$index->name} rate: {$value}");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("Error fetching {$index->name}: {$e->getMessage()}");
                Log::error("Error fetching index rate", [
                    'index' => $index->name,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $errorCount++;
            }
        }

        $this->info("Fetch completed: {$successCount} successful, {$errorCount} failed");

        if ($errorCount > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Extract a value from the API response using a dot notation path.
     *
     * @param array $data
     * @param string|null $path
     * @return float|null
     */
    protected function extractValueFromResponse($data, ?string $path): ?float
    {
        if (empty($path)) {
            // If no path is specified, assume the response is the value itself
            return is_numeric($data) ? (float) $data : null;
        }

        // Use dot notation to traverse the response array
        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (!isset($current[$segment])) {
                return null;
            }
            $current = $current[$segment];
        }

        return is_numeric($current) ? (float) $current : null;
    }
}
