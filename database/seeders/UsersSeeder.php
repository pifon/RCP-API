<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsersSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'id' => 1,
                'username' => 'system',
                'name' => 'System Account',
                'email' => 'przemek.wolski@gmail.com',
                'email_verified_at' => '2025-01-01 00:00:00',
                'password' => '***',
                'password_changed_at' => null,
                'token' => null,
                'created_at' => '2025-07-09 13:41:58',
                'updated_at' => '2025-07-09 13:41:58',
            ],
            [
                'id' => 2,
                'username' => 'test',
                'name' => 'Test Account',
                'email' => 'przemek.wolski@protonmail.com',
                'email_verified_at' => '2025-01-01 00:00:01',
                'password' => '***',
                'password_changed_at' => null,
                'token' => null,
                'created_at' => '2025-07-09 13:41:58',
                'updated_at' => '2025-07-09 13:41:58',
            ],
            [
                'id' => 3,
                'username' => 'author',
                'name' => 'Test Account Author',
                'email' => 'przemek.wolski@mail.com',
                'email_verified_at' => '2025-01-01 00:00:02',
                'password' => '***',
                'password_changed_at' => null,
                'token' => null,
                'created_at' => '2025-07-09 13:41:58',
                'updated_at' => '2025-07-09 13:41:58',
            ],
            [
                'id' => 4,
                'username' => 'test-user',
                'name' => 'Blake Wisozk MD',
                'email' => 'nhyatt@example.org',
                'email_verified_at' => null,
                'password' => '$2y$10$OU8J18Y/wW.9dFAR9rEaJetx/JNF/LxNWK68IWZVafyMsOhwcXZzy',
                'password_changed_at' => '2025-07-09 13:52:58',
                'token' => null,
                'created_at' => '2025-07-09 13:52:58',
                'updated_at' => '2025-07-09 13:52:58',
            ],
        ]);
    }
}
