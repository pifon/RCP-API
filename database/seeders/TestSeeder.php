<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Minimal seed data for the test database (SQLite).
 * Run after Doctrine schema creation so feature/integration tests have
 * cuisines, plans, products, measures, and dish types.
 */
class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlansSeeder::class,
            CuisinesSeeder::class,
            MeasuresSeeder::class,
            DishTypesSeeder::class,
            ProductsSeeder::class,
        ]);
    }
}
