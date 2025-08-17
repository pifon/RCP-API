<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * And set the most basic data
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        $this->call(AuthorsSeeder::class);
        $this->call(ElementTypesSeeder::class);
        $this->call(ElementSubTypesSeeder::class);
        $this->call(ElementsSeeder::class);
        $this->call(GroupsSeeder::class);
        $this->call(CuisinesSeeder::class);
        $this->call(DishTypesSeeder::class);
        $this->call(RecipesSeeder::class);
        $this->call(MeasuresSeeder::class);
        $this->call(DirectionsSeeder::class);
        $this->call(IngredientsSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(IngredientNotesSeeder::class);
        $this->call(OperationsSeeder::class);
        $this->call(ProceduresSeeder::class);
        $this->call(ServingsSeeder::class);
    }
}
