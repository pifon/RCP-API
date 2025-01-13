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
        $this->call(ElementTypesSeeder::class);
        $this->call(ElementSubTypesSeeder::class);
        $this->call(ElementsSeeder::class);
        $this->call(GroupsSeeder::class);
        $this->call(CuisineSeeder::class);
        $this->call(DishTypesSeeder::class);
        $this->call(RecipesSeeder::class);
    }
}
