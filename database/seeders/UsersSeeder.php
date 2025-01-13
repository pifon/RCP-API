<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(values: [
            [
                'name' => 'system',
                'email' => 'przemek.wolski@gmail.com',
                'email_verified_at' => '2025-01-01 00:00:00',
                'password' => '***',
            ],
            [
                'name' => 'test',
                'email' => 'przemek.wolski@protonmail.com',
                'email_verified_at' => '2025-01-01 00:00:01',
                'password' => '***',
            ],
        ]);
    }
}
