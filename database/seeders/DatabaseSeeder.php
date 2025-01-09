<?php

namespace Database\Seeders;

use App\Models\Element;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //$users = User::factory(10)->create();

        //Element::factory(10)
         //   ->recycle($users)
        //    ->create();

        User::factory()->create([
            'name' => 'System',
            'email' => 'system@pifon.com',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // RCP
        $this->call(ElementTypesSeeder::class);
        $this->call(ElementSubTypesSeeder::class);
        $this->call(ElementsSeeder::class);
        $this->call(GroupsSeeder::class);
        $this->call(CuisineSeeder::class);
        $this->call(DishTypesSeeder::class);
        $this->call(RecipesSeeder::class);
    }
}
