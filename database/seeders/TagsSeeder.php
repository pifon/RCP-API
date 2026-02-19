<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagsSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Meal context
            ['name' => 'Quick meal', 'group' => 'time'],
            ['name' => 'Under 30 minutes', 'group' => 'time'],
            ['name' => 'Weeknight dinner', 'group' => 'time'],
            ['name' => 'Meal prep', 'group' => 'time'],
            ['name' => 'One pot', 'group' => 'time'],

            // Occasion
            ['name' => 'Date night', 'group' => 'occasion'],
            ['name' => 'Party food', 'group' => 'occasion'],
            ['name' => 'Holiday', 'group' => 'occasion'],
            ['name' => 'Sunday roast', 'group' => 'occasion'],
            ['name' => 'BBQ', 'group' => 'occasion'],
            ['name' => 'Picnic', 'group' => 'occasion'],

            // Budget / lifestyle
            ['name' => 'Budget-friendly', 'group' => 'lifestyle'],
            ['name' => 'Comfort food', 'group' => 'lifestyle'],
            ['name' => 'Healthy', 'group' => 'lifestyle'],
            ['name' => 'High protein', 'group' => 'lifestyle'],
            ['name' => 'Low carb', 'group' => 'lifestyle'],
            ['name' => 'Kid-friendly', 'group' => 'lifestyle'],

            // Technique
            ['name' => 'No-bake', 'group' => 'technique'],
            ['name' => 'Slow cooker', 'group' => 'technique'],
            ['name' => 'Air fryer', 'group' => 'technique'],
            ['name' => 'Grilled', 'group' => 'technique'],
            ['name' => 'Raw', 'group' => 'technique'],
            ['name' => 'Fermented', 'group' => 'technique'],

            // Season
            ['name' => 'Summer', 'group' => 'season'],
            ['name' => 'Winter warmer', 'group' => 'season'],
            ['name' => 'Spring', 'group' => 'season'],
            ['name' => 'Autumn', 'group' => 'season'],
        ];

        $now = Carbon::now();

        $rows = array_map(fn (array $t) => [
            'name' => $t['name'],
            'slug' => Str::slug($t['name']),
            'group' => $t['group'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $tags);

        DB::table('tags')->insert($rows);
    }
}
