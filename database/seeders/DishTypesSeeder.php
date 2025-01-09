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
                'type' => 'dish',
            ],
            [
                'type' => 'snack',
            ],
            [
                'type' => 'breakfast',
            ],
            [
                'type' => 'lunch',
            ],
            [
                'type' => 'dinner',
            ],
            [
                'type' => 'dessert',
            ],
            [
                'type' => 'street-food',
            ],
            [
                'type' => 'beverage',
            ],
            [
                'type' => 'cocktail',
            ],
            [
                'type' => 'salad',
            ],
            [
                'type' => 'side',
            ],
            [
                'type' => 'sauce',
            ],
            [
                'type' => 'dip',
            ],
            [
                'type' => 'main',
            ],
            [
                'type' => 'soup',
            ],
            [
                'type' => 'spread',
            ],
            [
                'type' => 'nibble',
            ],
            [
                'type' => 'sandwich',
            ],
            [
                'type' => 'meze',
            ],
            [
                'type' => 'tapas',
            ],
            [
                'type' => 'cured meat',
            ],
            [
                'type' => 'pickles',
            ],
            [
                'type' => 'sweets',
            ],
            [
                'type' => 'meat',
            ],
            [
                'type' => 'condiment',
            ],
            [
                'type' => 'bread',
            ]
        ]);
    }
}
