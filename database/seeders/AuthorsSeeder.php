<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('authors')->insert(values: [
            [
                'name' => 'RCP System Author',
                'user_id' => 1,
                'email' => 'przemek.wolski@gmail.com',
            ],
            [
                'name' => 'Test Author',
                'user_id' => 3,
                'email' => 'przemek.wolski@gmail.com',
            ],
        ]);
    }
}
