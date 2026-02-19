<?php

// phpcs:disable Generic.Files.LineLength

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('plan_features')->truncate();
        DB::table('plans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('plans')->insert([
            [
                'id' => 1,
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic access to recipes and community features.',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'currency' => 'USD',
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Unlock premium recipes, advanced meal planning, and nutrition tracking.',
                'price_monthly' => 9.99,
                'price_yearly' => 99.99,
                'currency' => 'USD',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Everything in Pro plus exclusive content, priority support, and API access.',
                'price_monthly' => 19.99,
                'price_yearly' => 199.99,
                'currency' => 'USD',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('plan_features')->insert([
            ['plan_id' => 1, 'feature' => 'max_collections', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 1, 'feature' => 'max_shopping_lists', 'value' => '2', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 1, 'feature' => 'max_pantry_items', 'value' => '20', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 1, 'feature' => 'paid_recipes', 'value' => 'false', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 1, 'feature' => 'api_rate_limit', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],

            ['plan_id' => 2, 'feature' => 'max_collections', 'value' => '50', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 2, 'feature' => 'max_shopping_lists', 'value' => '20', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 2, 'feature' => 'max_pantry_items', 'value' => '200', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 2, 'feature' => 'paid_recipes', 'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 2, 'feature' => 'api_rate_limit', 'value' => '300', 'created_at' => now(), 'updated_at' => now()],

            ['plan_id' => 3, 'feature' => 'max_collections', 'value' => 'unlimited', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 3, 'feature' => 'max_shopping_lists', 'value' => 'unlimited', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 3, 'feature' => 'max_pantry_items', 'value' => 'unlimited', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 3, 'feature' => 'paid_recipes', 'value' => 'true', 'created_at' => now(), 'updated_at' => now()],
            ['plan_id' => 3, 'feature' => 'api_rate_limit', 'value' => '1000', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
