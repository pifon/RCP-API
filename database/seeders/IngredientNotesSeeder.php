<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IngredientNotesSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ingredient_notes')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('ingredient_notes')->insert([
            [
                'id' => 1,
                'ingredient_id' => 6,
                'note' => 'At room temperature',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'ingredient_id' => 3,
                'note' => 'or all-purpose flour',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 3,
                'ingredient_id' => 7,
                'note' => 'cut in halves',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 4,
                'ingredient_id' => 8,
                'note' => 'cut in half-moons',
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
