<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DishTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dish_types')->insert([
            [
                'name' => 'dish',
            ],
            [
                'name' => 'snack',
            ],
            [
                'name' => 'breakfast',
            ],
            [
                'name' => 'lunch',
            ],
            [
                'name' => 'dinner',
            ],
            [
                'name' => 'dessert',
            ],
            [
                'name' => 'street-food',
            ],
            [
                'name' => 'beverage',
            ],
            [
                'name' => 'cocktail',
            ],
            [
                'name' => 'salad',
            ],
            [
                'name' => 'side',
            ],
            [
                'name' => 'sauce',
            ],
            [
                'name' => 'dip',
            ],
            [
                'name' => 'main',
            ],
            [
                'name' => 'soup',
            ],
            [
                'name' => 'spread',
            ],
            [
                'name' => 'nibble',
            ],
            [
                'name' => 'sandwich',
            ],
            [
                'name' => 'meze',
            ],
            [
                'name' => 'tapas',
            ],
            [
                'name' => 'cured meat',
            ],
            [
                'name' => 'pickles',
            ],
            [
                'name' => 'sweets',
            ],
            [
                'name' => 'meat',
            ],
            [
                'name' => 'condiment',
            ],
            [
                'name' => 'bread',
            ]
        ]);
    }
}
