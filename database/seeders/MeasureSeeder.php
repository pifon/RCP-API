<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert base units first (they point to themselves as base_id)
        $units = [
            // id, name, abbr, type, base_id, factor
            ['id' => 1, 'name' => 'gram',      'abbr' => 'g',    'type' => 'M', 'base_id' => 1, 'factor' => 1],
            ['id' => 2, 'name' => 'milliliter', 'abbr' => 'ml',   'type' => 'V', 'base_id' => 2, 'factor' => 1],
            ['id' => 3, 'name' => 'meter',     'abbr' => 'm',    'type' => 'L', 'base_id' => 3, 'factor' => 1],
            ['id' => 4, 'name' => 'second',    'abbr' => 's',    'type' => 'T', 'base_id' => 4, 'factor' => 1],
            ['id' => 5, 'name' => 'piece',     'abbr' => 'pcs',  'type' => 'C', 'base_id' => 5, 'factor' => 1],
        ];

        // Derived units
        $units = array_merge($units, [
            ['id' => 6,  'name' => 'kilogram', 'abbr' => 'kg',  'type' => 'M', 'base_id' => 1, 'factor' => 1000],
            ['id' => 7,  'name' => 'ounce',    'abbr' => 'oz',  'type' => 'M', 'base_id' => 1, 'factor' => 28.3495],
            ['id' => 8,  'name' => 'pound',    'abbr' => 'lb',  'type' => 'M', 'base_id' => 1, 'factor' => 453.592],
            ['id' => 9,  'name' => 'liter',    'abbr' => 'l',   'type' => 'V', 'base_id' => 2, 'factor' => 1000],
            ['id' => 10, 'name' => 'cup',      'abbr' => 'cup', 'type' => 'V', 'base_id' => 2, 'factor' => 240],
            ['id' => 11, 'name' => 'tablespoon', 'abbr' => 'tbsp', 'type' => 'V', 'base_id' => 2, 'factor' => 15],
            ['id' => 12, 'name' => 'teaspoon', 'abbr' => 'tsp', 'type' => 'V', 'base_id' => 2, 'factor' => 5],
            ['id' => 13, 'name' => 'inch',     'abbr' => 'in',  'type' => 'L', 'base_id' => 3, 'factor' => 0.0254],
            ['id' => 14, 'name' => 'minute',   'abbr' => 'min', 'type' => 'T', 'base_id' => 4, 'factor' => 60],
            ['id' => 15, 'name' => 'handful',  'abbr' => 'hf',  'type' => 'O', 'base_id' => 5, 'factor' => 1],
            ['id' => 16, 'name' => 'pinch',    'abbr' => 'pinch', 'type' => 'O', 'base_id' => 1, 'factor' => 0.36],
            ['id' => 17, 'name' => 'splash',   'abbr' => 'spl', 'type' => 'O', 'base_id' => 2, 'factor' => 6],
            ['id' => 18, 'name' => 'bunch',    'abbr' => 'bch', 'type' => 'O', 'base_id' => 5, 'factor' => 1],
        ]);

        foreach ($units as $unit) {
            DB::table('measures')->insert([
                'name' => $unit['name'],
                'abbr' => $unit['abbr'],
                'measure_type' => $unit['type'],
                'base_id' => $unit['base_id'],
                'factor' => $unit['factor'],
            ]);
        }
    }
}
