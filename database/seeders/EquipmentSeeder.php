<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Oven',
            'Stovetop',
            'Microwave',
            'Grill',
            'Air fryer',
            'Slow cooker',
            'Pressure cooker',
            'Blender',
            'Food processor',
            'Stand mixer',
            'Hand mixer',
            'Mortar and pestle',
            'Rolling pin',
            'Baking sheet',
            'Cast iron skillet',
            'Frying pan',
            'Saucepan',
            'Stockpot',
            'Dutch oven',
            'Wok',
            'Colander',
            'Sieve',
            'Whisk',
            'Thermometer',
            'Mandoline',
            'Piping bag',
            'Blowtorch',
            'Sous vide circulator',
            'Steamer',
            'Deep fryer',
        ];

        $now = Carbon::now();

        $rows = array_map(fn (string $name) => [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $items);

        DB::table('equipment')->insert($rows);
    }
}
