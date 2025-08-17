<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorsSeeder extends Seeder
{
    public function run()
    {
        DB::table('authors')->truncate();

        DB::table('authors')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'RCP System Author',
                'email' => 'przemek.wolski@gmail.com',
                'description' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'name' => 'Test Author',
                'email' => 'przemek.wolski@gmail.com',
                'description' => null,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
