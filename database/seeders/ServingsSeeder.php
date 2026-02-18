<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServingsSeeder extends Seeder
{
    public function run()
    {
        DB::table('servings')->truncate();

        DB::table('servings')->insert([
            [
                'id' => 1,
                'product_id' => 1,
                'amount' => 1.0,
                'measure_id' => 12,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'product_id' => 2,
                'amount' => 30.0,
                'measure_id' => 2,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 3,
                'product_id' => 3,
                'amount' => 500.0,
                'measure_id' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 4,
                'product_id' => 4,
                'amount' => 2.0,
                'measure_id' => 12,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 5,
                'product_id' => 5,
                'amount' => 2.0,
                'measure_id' => 11,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 6,
                'product_id' => 2,
                'amount' => 400.0,
                'measure_id' => 2,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 7,
                'product_id' => 6,
                'amount' => 10.0,
                'measure_id' => 5,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 8,
                'product_id' => 7,
                'amount' => 1.0,
                'measure_id' => 23,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 9,
                'product_id' => 8,
                'amount' => 2.0,
                'measure_id' => 25,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
