<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProceduresSeeder extends Seeder
{
    public function run()
    {
        DB::table('procedures')->truncate();

        DB::table('procedures')->insert([
            [
                'id' => 7,
                'serving_id' => 1,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 8,
                'serving_id' => 2,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 9,
                'serving_id' => null,
                'operation_id' => 2,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 10,
                'serving_id' => null,
                'operation_id' => 8,
                'duration' => 5,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 16,
                'serving_id' => 3,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 17,
                'serving_id' => 4,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 18,
                'serving_id' => 5,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 19,
                'serving_id' => 6,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 20,
                'serving_id' => null,
                'operation_id' => 4,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 21,
                'serving_id' => null,
                'operation_id' => 8,
                'duration' => 30,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 22,
                'serving_id' => null,
                'operation_id' => 5,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 23,
                'serving_id' => null,
                'operation_id' => 6,
                'duration' => 720,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 24,
                'serving_id' => null,
                'operation_id' => 17,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 25,
                'serving_id' => null,
                'operation_id' => 11,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 26,
                'serving_id' => null,
                'operation_id' => 12,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 27,
                'serving_id' => null,
                'operation_id' => 9,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 28,
                'serving_id' => null,
                'operation_id' => 8,
                'duration' => 150,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 29,
                'serving_id' => 7,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 30,
                'serving_id' => 8,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 31,
                'serving_id' => 9,
                'operation_id' => 3,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 32,
                'serving_id' => null,
                'operation_id' => 11,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 33,
                'serving_id' => null,
                'operation_id' => 12,
                'duration' => 25,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 34,
                'serving_id' => null,
                'operation_id' => 13,
                'duration' => 5,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 35,
                'serving_id' => null,
                'operation_id' => 14,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 36,
                'serving_id' => null,
                'operation_id' => 13,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 37,
                'serving_id' => null,
                'operation_id' => 15,
                'duration' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
