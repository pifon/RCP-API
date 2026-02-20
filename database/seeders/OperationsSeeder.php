<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OperationsSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('operations')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('operations')->insert([
            [
                'id' => 1,
                'name' => 'get',
                'description' => 'Fetch item',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'name' => 'mix',
                'description' => 'mix together',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 3,
                'name' => 'add',
                'description' => 'add',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 4,
                'name' => 'foil cover',
                'description' => 'cover with foil',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 5,
                'name' => 'stretch',
                'description' => 'strech and fold',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 6,
                'name' => 'refrigerate',
                'description' => 'put into fridge',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 7,
                'name' => 'drizzle',
                'description' => 'drizzle',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 8,
                'name' => 'wait',
                'description' => 'wait',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 9,
                'name' => 'cloth cover',
                'description' => 'cover with cloth',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 10,
                'name' => 'sprinkle',
                'description' => 'sprinkle on top',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 11,
                'name' => 'preheat oven',
                'description' => 'heat up the oven',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 12,
                'name' => 'bake',
                'description' => 'bake in the oven',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 13,
                'name' => 'cool',
                'description' => 'leave to cool down',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 14,
                'name' => 'take out',
                'description' => 'take out from container',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 15,
                'name' => 'serve',
                'description' => 'is ready to server',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 16,
                'name' => 'set aside',
                'description' => 'set aside',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 17,
                'name' => 'put into form',
                'description' => 'put into form',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
