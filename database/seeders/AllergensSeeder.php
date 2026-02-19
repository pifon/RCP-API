<?php

// phpcs:disable Generic.Files.LineLength

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AllergensSeeder extends Seeder
{
    public function run(): void
    {
        $allergens = [
            ['name' => 'Gluten', 'description' => 'Found in wheat, barley, rye, and oats'],
            ['name' => 'Crustaceans', 'description' => 'Shrimp, crab, lobster, and similar shellfish'],
            ['name' => 'Eggs', 'description' => 'Chicken eggs and egg-derived products'],
            ['name' => 'Fish', 'description' => 'All species of fish and fish-derived products'],
            ['name' => 'Peanuts', 'description' => 'Peanuts and peanut-derived products'],
            ['name' => 'Soybeans', 'description' => 'Soybeans and soy-derived products'],
            ['name' => 'Dairy', 'description' => 'Milk and milk-derived products including lactose'],
            ['name' => 'Tree nuts', 'description' => 'Almonds, hazelnuts, walnuts, cashews, pecans, pistachios, macadamia, Brazil nuts'],
            ['name' => 'Celery', 'description' => 'Celery stalks, leaves, seeds, and celeriac'],
            ['name' => 'Mustard', 'description' => 'Mustard seeds, powder, oil, and prepared mustard'],
            ['name' => 'Sesame', 'description' => 'Sesame seeds and sesame-derived products'],
            ['name' => 'Sulphites', 'description' => 'Sulphur dioxide and sulphites at concentrations above 10mg/kg or 10mg/litre'],
            ['name' => 'Lupin', 'description' => 'Lupin seeds and lupin-derived products'],
            ['name' => 'Molluscs', 'description' => 'Mussels, clams, oysters, squid, snails, and octopus'],
        ];

        $now = Carbon::now();

        $rows = array_map(fn (array $a) => [
            'name' => $a['name'],
            'slug' => Str::slug($a['name']),
            'description' => $a['description'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $allergens);

        DB::table('allergens')->insert($rows);
    }
}
