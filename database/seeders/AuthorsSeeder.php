<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'username' => 'rcp',
                'user' => 1,
                'email' => 'przemek.wolski@gmail.com',
            ],
        ]);
    }
}
