<?php

// phpcs:disable Generic.Files.LineLength

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeEnrichmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProducts();
        $this->seedServings();
        $this->enrichRecipes();
        $this->seedIngredients();
        $this->seedOperations();
        $this->seedProcedures();
        $this->seedDirections();
    }

    private function seedProducts(): void
    {
        $products = [
            ['id' => 9,  'name' => 'all-purpose flour',  'slug' => 'flour-all-purpose',  'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'sugar',              'slug' => 'sugar-white',         'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'butter',             'slug' => 'butter-unsalted',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'egg',                'slug' => 'egg-chicken',         'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'whole milk',         'slug' => 'milk-whole',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'mozzarella',         'slug' => 'mozzarella',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => 'tomato passata',     'slug' => 'tomato-passata',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'name' => 'garlic',             'slug' => 'garlic',              'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'name' => 'basil',              'slug' => 'basil-fresh',         'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'name' => 'black pepper',       'slug' => 'pepper-black',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => 'parmesan',           'slug' => 'parmesan',            'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'name' => 'onion',              'slug' => 'onion-yellow',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'name' => 'chicken breast',     'slug' => 'chicken-breast',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'name' => 'rice',               'slug' => 'rice-white',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'name' => 'spaghetti pasta',    'slug' => 'spaghetti-dried',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'name' => 'arborio rice',       'slug' => 'rice-arborio',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'name' => 'white wine',         'slug' => 'wine-white-dry',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'name' => 'chicken stock',      'slug' => 'stock-chicken',       'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'name' => 'soy sauce',          'slug' => 'soy-sauce',           'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'name' => 'ginger',             'slug' => 'ginger-fresh',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'name' => 'sesame oil',         'slug' => 'sesame-oil',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'name' => 'tortilla',           'slug' => 'tortilla-corn',       'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'name' => 'avocado',            'slug' => 'avocado',             'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'name' => 'lime',               'slug' => 'lime',                'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'name' => 'coriander',          'slug' => 'coriander-fresh',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'name' => 'chili flakes',       'slug' => 'chili-flakes',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'name' => 'cream',              'slug' => 'cream-double',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'name' => 'mascarpone',         'slug' => 'mascarpone',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'name' => 'espresso coffee',    'slug' => 'coffee-espresso',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'name' => 'cocoa powder',       'slug' => 'cocoa-powder',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 39, 'name' => 'ladyfinger biscuit', 'slug' => 'savoiardi',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'name' => 'chickpeas',          'slug' => 'chickpeas-dried',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'name' => 'tahini',             'slug' => 'tahini',              'created_at' => now(), 'updated_at' => now()],
            ['id' => 42, 'name' => 'lemon',              'slug' => 'lemon',               'created_at' => now(), 'updated_at' => now()],
            ['id' => 43, 'name' => 'cumin',              'slug' => 'cumin-ground',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 44, 'name' => 'noodles',            'slug' => 'noodles-ramen',       'created_at' => now(), 'updated_at' => now()],
            ['id' => 45, 'name' => 'pork belly',         'slug' => 'pork-belly',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 46, 'name' => 'spring onion',       'slug' => 'spring-onion',        'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($products as $p) {
            DB::table('products')->updateOrInsert(['id' => $p['id']], $p);
        }
    }

    private function seedServings(): void
    {
        $servings = [
            ['id' => 10, 'product_id' => 3,  'amount' => 500,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'product_id' => 1,  'amount' => 7,    'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'product_id' => 4,  'amount' => 10,   'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'product_id' => 2,  'amount' => 325,  'measure_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'product_id' => 5,  'amount' => 30,   'measure_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'product_id' => 15, 'amount' => 200,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'product_id' => 14, 'amount' => 200,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'product_id' => 17, 'amount' => 1,    'measure_id' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'product_id' => 23, 'amount' => 400,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'product_id' => 16, 'amount' => 3,    'measure_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'product_id' => 18, 'amount' => 1,    'measure_id' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'product_id' => 19, 'amount' => 80,   'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'product_id' => 24, 'amount' => 350,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'product_id' => 20, 'amount' => 1,    'measure_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'product_id' => 11, 'amount' => 30,   'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'product_id' => 25, 'amount' => 120,  'measure_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'product_id' => 26, 'amount' => 1000, 'measure_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'product_id' => 36, 'amount' => 500,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'product_id' => 12, 'amount' => 4,    'measure_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'product_id' => 10, 'amount' => 100,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'product_id' => 37, 'amount' => 300,  'measure_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'product_id' => 38, 'amount' => 30,   'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'product_id' => 39, 'amount' => 30,   'measure_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'product_id' => 40, 'amount' => 400,  'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'product_id' => 41, 'amount' => 60,   'measure_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'product_id' => 42, 'amount' => 1,    'measure_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'product_id' => 43, 'amount' => 1,    'measure_id' => 12, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($servings as $s) {
            DB::table('servings')->updateOrInsert(['id' => $s['id']], $s);
        }
    }

    private function enrichRecipes(): void
    {
        $updates = [
            ['id' => 1,  'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 120, 'cook_time_minutes' => 15, 'serves' => 4, 'cuisine_id' => 6,  'dish_type_id' => 14],
            ['id' => 2,  'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 5,   'cook_time_minutes' => 12, 'serves' => 4, 'cuisine_id' => 1,  'dish_type_id' => 14],
            ['id' => 3,  'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 5,   'cook_time_minutes' => 10, 'serves' => 4, 'cuisine_id' => 1,  'dish_type_id' => 14],
            ['id' => 4,  'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 10,  'cook_time_minutes' => 25, 'serves' => 4, 'cuisine_id' => 2,  'dish_type_id' => 14],
            ['id' => 5,  'status' => 'published', 'difficulty' => 'hard',   'prep_time_minutes' => 30,  'cook_time_minutes' => 0,  'serves' => 6, 'cuisine_id' => 1,  'dish_type_id' => 6],
            ['id' => 6,  'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 40,  'cook_time_minutes' => 0,  'serves' => 8, 'cuisine_id' => 8,  'dish_type_id' => 6],
            ['id' => 7,  'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 45,  'cook_time_minutes' => 15, 'serves' => 6, 'cuisine_id' => 14, 'dish_type_id' => 14],
            ['id' => 8,  'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 20,  'cook_time_minutes' => 25, 'serves' => 4, 'cuisine_id' => 15, 'dish_type_id' => 14],
            ['id' => 9,  'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 10,  'cook_time_minutes' => 10, 'serves' => 4, 'cuisine_id' => 14, 'dish_type_id' => 14],
            ['id' => 10, 'status' => 'published', 'difficulty' => 'expert', 'prep_time_minutes' => 60,  'cook_time_minutes' => 90, 'serves' => 6, 'cuisine_id' => 14, 'dish_type_id' => 14],
            ['id' => 11, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 15,  'cook_time_minutes' => 15, 'serves' => 4, 'cuisine_id' => 16, 'dish_type_id' => 14],
            ['id' => 12, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 20,  'cook_time_minutes' => 40, 'serves' => 6, 'cuisine_id' => 28, 'dish_type_id' => 14],
            ['id' => 13, 'status' => 'published', 'difficulty' => 'hard',   'prep_time_minutes' => 30,  'cook_time_minutes' => 45, 'serves' => 8, 'cuisine_id' => 39, 'dish_type_id' => 14],
            ['id' => 14, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 30,  'cook_time_minutes' => 20, 'serves' => 12, 'cuisine_id' => 27, 'dish_type_id' => 2],
            ['id' => 15, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 15,  'cook_time_minutes' => 10, 'serves' => 8, 'cuisine_id' => 32, 'dish_type_id' => 26],
            ['id' => 16, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 25,  'cook_time_minutes' => 30, 'serves' => 4, 'cuisine_id' => 28, 'dish_type_id' => 14],
            ['id' => 17, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 15,  'cook_time_minutes' => 5,  'serves' => 4, 'cuisine_id' => 41, 'dish_type_id' => 7],
            ['id' => 18, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 20,  'cook_time_minutes' => 5,  'serves' => 4, 'cuisine_id' => 41, 'dish_type_id' => 14],
            ['id' => 19, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 20,  'cook_time_minutes' => 20, 'serves' => 4, 'cuisine_id' => 41, 'dish_type_id' => 14],
            ['id' => 20, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 15,  'cook_time_minutes' => 0,  'serves' => 6, 'cuisine_id' => 41, 'dish_type_id' => 13],
            ['id' => 22, 'status' => 'published', 'difficulty' => 'expert', 'prep_time_minutes' => 60,  'cook_time_minutes' => 0,  'serves' => 4, 'cuisine_id' => 53, 'dish_type_id' => 14],
            ['id' => 24, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 30,  'cook_time_minutes' => 360, 'serves' => 4, 'cuisine_id' => 53, 'dish_type_id' => 15],
            ['id' => 30, 'status' => 'published', 'difficulty' => 'hard',   'prep_time_minutes' => 20,  'cook_time_minutes' => 120, 'serves' => 6, 'cuisine_id' => 58, 'dish_type_id' => 14],
            ['id' => 31, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 15,  'cook_time_minutes' => 45,  'serves' => 4, 'cuisine_id' => 55, 'dish_type_id' => 6],
            ['id' => 32, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 20,  'cook_time_minutes' => 10,  'serves' => 4, 'cuisine_id' => 64, 'dish_type_id' => 14],
            ['id' => 33, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 15,  'cook_time_minutes' => 25,  'serves' => 4, 'cuisine_id' => 64, 'dish_type_id' => 14],
            ['id' => 34, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 10,  'cook_time_minutes' => 15,  'serves' => 4, 'cuisine_id' => 64, 'dish_type_id' => 15],
            ['id' => 36, 'status' => 'published', 'difficulty' => 'hard',   'prep_time_minutes' => 30,  'cook_time_minutes' => 45,  'serves' => 6, 'cuisine_id' => 74, 'dish_type_id' => 14],
            ['id' => 40, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 15,  'cook_time_minutes' => 0,   'serves' => 8, 'cuisine_id' => 78, 'dish_type_id' => 13],
            ['id' => 41, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 20,  'cook_time_minutes' => 15,  'serves' => 6, 'cuisine_id' => 78, 'dish_type_id' => 7],
            ['id' => 43, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 30,  'cook_time_minutes' => 0,   'serves' => 8, 'cuisine_id' => 83, 'dish_type_id' => 22],
            ['id' => 44, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 20,  'cook_time_minutes' => 10,  'serves' => 4, 'cuisine_id' => 83, 'dish_type_id' => 14],
            ['id' => 45, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 20,  'cook_time_minutes' => 5,   'serves' => 4, 'cuisine_id' => 84, 'dish_type_id' => 14],
            ['id' => 47, 'status' => 'published', 'difficulty' => 'hard',   'prep_time_minutes' => 30,  'cook_time_minutes' => 60,  'serves' => 6, 'cuisine_id' => 88, 'dish_type_id' => 14],
            ['id' => 48, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 15,  'cook_time_minutes' => 10,  'serves' => 4, 'cuisine_id' => 88, 'dish_type_id' => 7],
            ['id' => 49, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 10,  'cook_time_minutes' => 0,   'serves' => 6, 'cuisine_id' => 88, 'dish_type_id' => 13],
            ['id' => 50, 'status' => 'published', 'difficulty' => 'hard',   'prep_time_minutes' => 45,  'cook_time_minutes' => 45,  'serves' => 12, 'cuisine_id' => 88, 'dish_type_id' => 23],
            ['id' => 54, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 30,  'cook_time_minutes' => 360, 'serves' => 4, 'cuisine_id' => 100, 'dish_type_id' => 15],
            ['id' => 55, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 15,  'cook_time_minutes' => 5,   'serves' => 4, 'cuisine_id' => 100, 'dish_type_id' => 18],
            ['id' => 65, 'status' => 'published', 'difficulty' => 'medium', 'prep_time_minutes' => 120, 'cook_time_minutes' => 12,  'serves' => 4, 'cuisine_id' => 6,   'dish_type_id' => 14],
            ['id' => 69, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 180, 'cook_time_minutes' => 25,  'serves' => 8, 'cuisine_id' => 12,  'dish_type_id' => 26],
            ['id' => 70, 'status' => 'published', 'difficulty' => 'easy',   'prep_time_minutes' => 180, 'cook_time_minutes' => 30,  'serves' => 8, 'cuisine_id' => 10,  'dish_type_id' => 26],
        ];

        foreach ($updates as $u) {
            $id = $u['id'];
            unset($u['id']);
            $u['updated_at'] = now();
            DB::table('recipes')->where('id', $id)->update($u);
        }
    }

    private function seedIngredients(): void
    {
        $ingredients = [
            // Pizza Margherita (recipe 65): flour, yeast, salt, water, olive oil, passata, mozzarella, basil
            ['id' => 10, 'recipe_id' => 65, 'serving_id' => 10, 'position' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'recipe_id' => 65, 'serving_id' => 11, 'position' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'recipe_id' => 65, 'serving_id' => 12, 'position' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'recipe_id' => 65, 'serving_id' => 13, 'position' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'recipe_id' => 65, 'serving_id' => 14, 'position' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'recipe_id' => 65, 'serving_id' => 15, 'position' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'recipe_id' => 65, 'serving_id' => 16, 'position' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'recipe_id' => 65, 'serving_id' => 17, 'position' => 8, 'created_at' => now(), 'updated_at' => now()],

            // Spaghetti (recipe 3): pasta, garlic, olive oil, pepper, parmesan
            ['id' => 18, 'recipe_id' => 3,  'serving_id' => 18, 'position' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'recipe_id' => 3,  'serving_id' => 19, 'position' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'recipe_id' => 3,  'serving_id' => 14, 'position' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'recipe_id' => 3,  'serving_id' => 20, 'position' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'recipe_id' => 3,  'serving_id' => 21, 'position' => 5, 'created_at' => now(), 'updated_at' => now()],

            // Risotto (recipe 4): arborio rice, onion, butter, white wine, stock, parmesan
            ['id' => 23, 'recipe_id' => 4,  'serving_id' => 22, 'position' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'recipe_id' => 4,  'serving_id' => 23, 'position' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'recipe_id' => 4,  'serving_id' => 24, 'position' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'recipe_id' => 4,  'serving_id' => 25, 'position' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'recipe_id' => 4,  'serving_id' => 26, 'position' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'recipe_id' => 4,  'serving_id' => 21, 'position' => 6, 'created_at' => now(), 'updated_at' => now()],

            // Tiramisu (recipe 6): mascarpone, eggs, sugar, espresso, cocoa, savoiardi
            ['id' => 29, 'recipe_id' => 6,  'serving_id' => 27, 'position' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'recipe_id' => 6,  'serving_id' => 28, 'position' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'recipe_id' => 6,  'serving_id' => 29, 'position' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'recipe_id' => 6,  'serving_id' => 30, 'position' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'recipe_id' => 6,  'serving_id' => 31, 'position' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'recipe_id' => 6,  'serving_id' => 32, 'position' => 6, 'created_at' => now(), 'updated_at' => now()],

            // Hummus (recipe 40): chickpeas, tahini, lemon, garlic, cumin, olive oil
            ['id' => 35, 'recipe_id' => 40, 'serving_id' => 33, 'position' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'recipe_id' => 40, 'serving_id' => 34, 'position' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'recipe_id' => 40, 'serving_id' => 35, 'position' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'recipe_id' => 40, 'serving_id' => 19, 'position' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 39, 'recipe_id' => 40, 'serving_id' => 36, 'position' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'recipe_id' => 40, 'serving_id' => 14, 'position' => 6, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($ingredients as $i) {
            DB::table('ingredients')->updateOrInsert(['id' => $i['id']], $i);
        }
    }

    private function seedOperations(): void
    {
        $existing = DB::table('operations')->pluck('id', 'name')->toArray();
        $ops = ['sieve', 'dissolve', 'combine', 'knead', 'prove', 'shape', 'spread', 'top', 'bake', 'boil', 'drain', 'toss', 'fry', 'stir', 'simmer', 'ladle', 'separate', 'whisk', 'fold', 'dip', 'layer', 'chill', 'dust', 'soak', 'blend', 'squeeze', 'drizzle', 'garnish'];

        foreach ($ops as $name) {
            if (! isset($existing[$name])) {
                DB::table('operations')->insert([
                    'name' => $name, 'description' => $name, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedProcedures(): void
    {
        $opIds = DB::table('operations')->pluck('id', 'name')->toArray();

        $procedures = [
            // Spaghetti aglio e olio directions
            ['id' => 50, 'operation_id' => $opIds['boil'],    'serving_id' => null, 'duration' => 10],
            ['id' => 51, 'operation_id' => $opIds['fry'],     'serving_id' => null, 'duration' => 3],
            ['id' => 52, 'operation_id' => $opIds['drain'],   'serving_id' => null, 'duration' => 1],
            ['id' => 53, 'operation_id' => $opIds['toss'],    'serving_id' => null, 'duration' => 2],

            // Risotto directions
            ['id' => 54, 'operation_id' => $opIds['fry'],     'serving_id' => null, 'duration' => 3],
            ['id' => 55, 'operation_id' => $opIds['stir'],    'serving_id' => null, 'duration' => 2],
            ['id' => 56, 'operation_id' => $opIds['simmer'],  'serving_id' => null, 'duration' => 3],
            ['id' => 57, 'operation_id' => $opIds['ladle'],   'serving_id' => null, 'duration' => 18],
            ['id' => 58, 'operation_id' => $opIds['fold'],    'serving_id' => null, 'duration' => 2],

            // Tiramisu directions
            ['id' => 59, 'operation_id' => $opIds['separate'], 'serving_id' => null, 'duration' => 5],
            ['id' => 60, 'operation_id' => $opIds['whisk'],    'serving_id' => null, 'duration' => 5],
            ['id' => 61, 'operation_id' => $opIds['fold'],     'serving_id' => null, 'duration' => 3],
            ['id' => 62, 'operation_id' => $opIds['dip'],      'serving_id' => null, 'duration' => 5],
            ['id' => 63, 'operation_id' => $opIds['layer'],    'serving_id' => null, 'duration' => 5],
            ['id' => 64, 'operation_id' => $opIds['chill'],    'serving_id' => null, 'duration' => 240],
            ['id' => 65, 'operation_id' => $opIds['dust'],     'serving_id' => null, 'duration' => 1],

            // Hummus directions
            ['id' => 66, 'operation_id' => $opIds['soak'],    'serving_id' => null, 'duration' => 480],
            ['id' => 67, 'operation_id' => $opIds['boil'],    'serving_id' => null, 'duration' => 60],
            ['id' => 68, 'operation_id' => $opIds['blend'],   'serving_id' => null, 'duration' => 3],
            ['id' => 69, 'operation_id' => $opIds['squeeze'], 'serving_id' => null, 'duration' => 1],
            ['id' => 70, 'operation_id' => $opIds['blend'],   'serving_id' => null, 'duration' => 2],
            ['id' => 71, 'operation_id' => $opIds['drizzle'], 'serving_id' => null, 'duration' => 1],
        ];

        foreach ($procedures as $p) {
            $p['created_at'] = now();
            $p['updated_at'] = now();
            DB::table('procedures')->updateOrInsert(['id' => $p['id']], $p);
        }
    }

    private function seedDirections(): void
    {
        $directions = [
            // Spaghetti aglio e olio (recipe 3)
            ['id' => 50, 'recipe_id' => 3,  'procedure_id' => 50, 'ingredient_id' => 18, 'sequence' => 1],
            ['id' => 51, 'recipe_id' => 3,  'procedure_id' => 51, 'ingredient_id' => 19, 'sequence' => 2],
            ['id' => 52, 'recipe_id' => 3,  'procedure_id' => 52, 'ingredient_id' => 18, 'sequence' => 3],
            ['id' => 53, 'recipe_id' => 3,  'procedure_id' => 53, 'ingredient_id' => null, 'sequence' => 4],

            // Risotto (recipe 4)
            ['id' => 54, 'recipe_id' => 4,  'procedure_id' => 54, 'ingredient_id' => 24, 'sequence' => 1],
            ['id' => 55, 'recipe_id' => 4,  'procedure_id' => 55, 'ingredient_id' => 23, 'sequence' => 2],
            ['id' => 56, 'recipe_id' => 4,  'procedure_id' => 56, 'ingredient_id' => 26, 'sequence' => 3],
            ['id' => 57, 'recipe_id' => 4,  'procedure_id' => 57, 'ingredient_id' => 27, 'sequence' => 4],
            ['id' => 58, 'recipe_id' => 4,  'procedure_id' => 58, 'ingredient_id' => 28, 'sequence' => 5],

            // Tiramisu (recipe 6)
            ['id' => 59, 'recipe_id' => 6,  'procedure_id' => 59, 'ingredient_id' => 30, 'sequence' => 1],
            ['id' => 60, 'recipe_id' => 6,  'procedure_id' => 60, 'ingredient_id' => 31, 'sequence' => 2],
            ['id' => 61, 'recipe_id' => 6,  'procedure_id' => 61, 'ingredient_id' => 29, 'sequence' => 3],
            ['id' => 62, 'recipe_id' => 6,  'procedure_id' => 62, 'ingredient_id' => 34, 'sequence' => 4],
            ['id' => 63, 'recipe_id' => 6,  'procedure_id' => 63, 'ingredient_id' => null, 'sequence' => 5],
            ['id' => 64, 'recipe_id' => 6,  'procedure_id' => 64, 'ingredient_id' => null, 'sequence' => 6],
            ['id' => 65, 'recipe_id' => 6,  'procedure_id' => 65, 'ingredient_id' => 33, 'sequence' => 7],

            // Hummus (recipe 40)
            ['id' => 66, 'recipe_id' => 40, 'procedure_id' => 66, 'ingredient_id' => 35, 'sequence' => 1],
            ['id' => 67, 'recipe_id' => 40, 'procedure_id' => 67, 'ingredient_id' => 35, 'sequence' => 2],
            ['id' => 68, 'recipe_id' => 40, 'procedure_id' => 68, 'ingredient_id' => 36, 'sequence' => 3],
            ['id' => 69, 'recipe_id' => 40, 'procedure_id' => 69, 'ingredient_id' => 37, 'sequence' => 4],
            ['id' => 70, 'recipe_id' => 40, 'procedure_id' => 70, 'ingredient_id' => null, 'sequence' => 5],
            ['id' => 71, 'recipe_id' => 40, 'procedure_id' => 71, 'ingredient_id' => 40, 'sequence' => 6],
        ];

        foreach ($directions as $d) {
            $d['created_at'] = now();
            $d['updated_at'] = now();
            DB::table('directions')->updateOrInsert(['id' => $d['id']], $d);
        }
    }
}
