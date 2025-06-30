<?php

namespace Database\Seeders;

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
                'username' => 'system',
                'name' => 'System Account',
                'email' => 'przemek.wolski@gmail.com',
                'email_verified_at' => '2025-01-01 00:00:00',
                'password' => '***',
            ],
            [
                'username' => 'test',
                'name' => 'Test Account',
                'email' => 'przemek.wolski@protonmail.com',
                'email_verified_at' => '2025-01-01 00:00:01',
                'password' => '***',
            ],
            [
                'username' => 'author',
                'name' => 'Test Account Author',
                'email' => 'przemek.wolski@mail.com',
                'email_verified_at' => '2025-01-01 00:00:02',
                'password' => '***',
            ],
        ]);
    }
}
