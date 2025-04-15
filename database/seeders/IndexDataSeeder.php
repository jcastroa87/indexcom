<?php

namespace Database\Seeders;

use App\Models\Index;
use App\Models\Rate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class IndexDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test admin user with API key
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'api_key' => Str::random(32),
                'is_active' => true,
            ]
        );

        // Create some sample indices
        $indices = [
            [
                'name' => 'US Dollar',
                'slug' => 'usd',
                'description' => 'USD to local currency exchange rate',
                'source_api_url' => 'https://api.exchangerate-api.com/v4/latest/USD',
                'source_api_path' => 'rates.CLP',
                'is_active' => true,
                'fetch_frequency' => 60, // 1 hour
            ],
            [
                'name' => 'Bitcoin',
                'slug' => 'bitcoin',
                'description' => 'Bitcoin (BTC) price in USD',
                'source_api_url' => 'https://api.coindesk.com/v1/bpi/currentprice.json',
                'source_api_path' => 'bpi.USD.rate_float',
                'is_active' => true,
                'fetch_frequency' => 30, // 30 minutes
            ],
            [
                'name' => 'UF',
                'slug' => 'uf',
                'description' => 'Unidad de Fomento - Chilean inflation-indexed unit',
                'source_api_url' => 'https://mindicador.cl/api/uf',
                'source_api_path' => 'serie.0.valor',
                'is_active' => true,
                'fetch_frequency' => 1440, // 24 hours
            ],
        ];

        foreach ($indices as $indexData) {
            $index = Index::firstOrCreate(
                ['slug' => $indexData['slug']],
                $indexData
            );

            // Generate some historical rate data for the past 30 days
            $this->generateHistoricalRates($index);
        }
    }

    /**
     * Generate sample historical rate data for an index.
     *
     * @param Index $index
     * @return void
     */
    private function generateHistoricalRates(Index $index): void
    {
        // Skip if rates already exist
        if ($index->rates()->count() > 0) {
            $this->command->info("Rates already exist for {$index->name}. Skipping generation.");
            return;
        }

        $this->command->info("Generating historical rates for {$index->name}...");

        // Generate data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $baseValue = $this->getBaseValueForIndex($index);

        for ($i = 0; $i < 30; $i++) {
            $date = (clone $startDate)->addDays($i);

            // Add some random variation to simulate real data changes
            // Different volatility for different indices
            $volatility = $this->getVolatilityForIndex($index);
            $randomFactor = 1 + (mt_rand(-$volatility, $volatility) / 10000);
            $value = $baseValue * $randomFactor;

            // Create the rate record
            Rate::create([
                'index_id' => $index->id,
                'date' => $date,
                'value' => $value,
                'is_manual' => true, // This is seeded data
            ]);

            // Base value for next iteration (simulates trend)
            $trendFactor = 1 + (mt_rand(-5, 5) / 10000);
            $baseValue *= $trendFactor;
        }

        $this->command->info("Successfully generated 30 days of historical data for {$index->name}");
    }

    /**
     * Get a reasonable base value for each index type.
     *
     * @param Index $index
     * @return float
     */
    private function getBaseValueForIndex(Index $index): float
    {
        return match ($index->slug) {
            'usd' => 850.0,     // USD value in CLP
            'bitcoin' => 60000.0, // BTC value in USD
            'uf' => 35000.0,    // UF value in CLP
            default => 100.0,   // Default value
        };
    }

    /**
     * Get reasonable volatility for each index type.
     * Higher values mean more volatile data.
     *
     * @param Index $index
     * @return int
     */
    private function getVolatilityForIndex(Index $index): int
    {
        return match ($index->slug) {
            'usd' => 30,      // USD is moderately volatile
            'bitcoin' => 150, // Bitcoin is highly volatile
            'uf' => 5,        // UF is very stable
            default => 20,    // Default volatility
        };
    }
}
