<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default plans for API users
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic API access with limited features and request volume.',
                'price' => 0.00,
                'request_limit' => 50,
                'has_extended_data' => false,
                'features' => json_encode(['Basic API access', 'Daily rate data', 'Latest index values']),
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Standard API access with moderate request volume for small applications.',
                'price' => 29.99,
                'request_limit' => 500,
                'has_extended_data' => false,
                'features' => json_encode(['Everything in Free', 'Increased request limit', 'Historical data access', 'Email support']),
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Advanced API access with high request volume and extended data for business applications.',
                'price' => 99.99,
                'request_limit' => 2000,
                'has_extended_data' => true,
                'features' => json_encode(['Everything in Basic', 'Extended data access', 'Higher request limit', 'Data analysis endpoints', 'Priority support']),
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited API access with all premium features for large-scale applications.',
                'price' => 249.99,
                'request_limit' => 10000,
                'has_extended_data' => true,
                'features' => json_encode(['Everything in Professional', 'Highest request limit', 'Custom data integrations', 'Dedicated support', 'SLA guarantee']),
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
