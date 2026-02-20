<?php

// phpcs:disable Generic.Files.LineLength

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SensationsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('sensations')->truncate();
        Schema::enableForeignKeyConstraints();

        $sensations = [
            ['name' => 'Spicy', 'description' => 'Heat from capsaicin or similar compounds (chili, pepper, wasabi)'],
            ['name' => 'Fatty', 'description' => 'Richness and mouthcoating from fats and oils'],
            ['name' => 'Watery', 'description' => 'Thin, light, hydrating mouthfeel'],
            ['name' => 'Metallic', 'description' => 'Tinny or iron-like sensation (beets, liver, some seafood)'],
            ['name' => 'Acidic', 'description' => 'Sharp, tangy bite beyond basic sourness (vinegar, citrus zest)'],
            ['name' => 'Smoky', 'description' => 'Charred, wood-fire, or smoke-cured quality'],
            ['name' => 'Astringent', 'description' => 'Drying, puckering mouthfeel (tannins in tea, unripe fruit, red wine)'],
            ['name' => 'Cooling', 'description' => 'Menthol-like freshness (mint, cucumber, eucalyptus)'],
            ['name' => 'Creamy', 'description' => 'Smooth, velvety, dairy-like richness'],
            ['name' => 'Crunchy', 'description' => 'Textural crispness and snap'],
            ['name' => 'Numbing', 'description' => 'Tingling, mouth-numbing sensation (Sichuan pepper, clove)'],
            ['name' => 'Earthy', 'description' => 'Soil-like, mushroom, root vegetable depth'],
            ['name' => 'Floral', 'description' => 'Flower-like, perfumed aroma and taste (rose, lavender, elderflower)'],
            ['name' => 'Herbaceous', 'description' => 'Fresh green herb quality (basil, cilantro, dill)'],
            ['name' => 'Nutty', 'description' => 'Toasted, roasted nut character (almonds, sesame, browned butter)'],
            ['name' => 'Fermented', 'description' => 'Tangy, complex depth from fermentation (kimchi, miso, sourdough)'],
        ];

        $now = Carbon::now();

        $rows = array_map(fn (array $s) => [
            'name' => $s['name'],
            'slug' => Str::slug($s['name']),
            'description' => $s['description'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $sensations);

        DB::table('sensations')->insert($rows);
    }
}
