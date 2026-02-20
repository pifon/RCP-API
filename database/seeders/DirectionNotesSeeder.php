<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DirectionNotesSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('direction_notes')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('direction_notes')->insert([
            [
                'id' => 1,
                'direction_id' => 3,
                'note' => 'in a large bowl',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'direction_id' => 9,
                'note' => 'mix well, but gently',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 3,
                'direction_id' => 9,
                'note' => 'at the end dough should be sticky',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 4,
                'direction_id' => 10,
                'note' => 'to not let dry',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 5,
                'direction_id' => 12,
                'note' => 'with wet hands',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 6,
                'direction_id' => 15,
                'note' => 'with wet hands',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 7,
                'direction_id' => 18,
                'note' => 'with wet hands',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 8,
                'direction_id' => 20,
                'note' => 'min 12 hours and up to 2 days',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 9,
                'direction_id' => 20,
                'note' => 'refrigerating can be skipped, but it adds depth to taste',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 10,
                'direction_id' => 26,
                'note' => '2-3 hours. it should grow a bit',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 11,
                'direction_id' => 34,
                'note' => 'let it cool completely',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
