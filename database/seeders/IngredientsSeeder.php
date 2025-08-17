<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientsSeeder extends Seeder
{
    public function run()
    {
        DB::table('ingredients')->truncate();

        DB::table('ingredients')->insert([
            [
                'id' => 1,
                'recipe_id' => 70,
                'serving_id' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'recipe_id' => 70,
                'serving_id' => 2,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 3,
                'recipe_id' => 70,
                'serving_id' => 3,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 4,
                'recipe_id' => 70,
                'serving_id' => 4,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 5,
                'recipe_id' => 70,
                'serving_id' => 5,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 6,
                'recipe_id' => 70,
                'serving_id' => 6,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 7,
                'recipe_id' => 70,
                'serving_id' => 7,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 8,
                'recipe_id' => 70,
                'serving_id' => 8,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 9,
                'recipe_id' => 70,
                'serving_id' => 9,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
