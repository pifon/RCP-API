<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('recipes')->insert(values: [
            [
                'slug' => 'pizza',
                'variant' => null,
                'author' => 1,
                'title' => 'Pizza',
                'description' => null,
                'cuisine' => 1,
                'type' => 1
            ],
            [
                'slug' => 'pasta',
                'variant' => null,
                'author' => 1,
                'title' => 'Pasta',
                'description' => null,
                'cuisine' => 1,
                'type' => 1
            ],
            [
                'slug' => 'spaghetti',
                'variant' => null,
                'author' => 1,
                'title' => 'Spaghetti',
                'description' => null,
                'cuisine' => 1,
                'type' => 1
            ],
            [
                'slug' => 'risotto',
                'variant' => null,
                'author' => 1,
                'title' => 'Risotto',
                'description' => null,
                'cuisine' => 1,
                'type' => 1
            ],
            [
                'slug' => 'gelato',
                'variant' => null,
                'author' => 1,
                'title' => 'Gelato',
                'description' => null,
                'cuisine' => 1,
                'type' => 6
            ],
            [
                'slug' => 'tiramisu',
                'variant' => null,
                'author' => 1,
                'title' => 'Tiramisu',
                'description' => null,
                'cuisine' => 1,
                'type' => 6
            ],
            [
                'slug' => 'dumplings',
                'variant' => null,
                'author' => 1,
                'title' => 'Dumplings',
                'description' => null,
                'cuisine' => 14,
                'type' => 1
            ],
            [
                'slug' => 'sweet-and-sour-pork',
                'variant' => null,
                'author' => 1,
                'title' => 'Sweet and Sour Pork',
                'description' => null,
                'cuisine' => 14,
                'type' => 1
            ],
            [
                'slug' => 'fried-rice',
                'variant' => null,
                'author' => 1,
                'title' => 'Fried Rice',
                'description' => null,
                'cuisine' => 14,
                'type' => 1
            ],
            [
                'slug' => 'peking-duck',
                'variant' => null,
                'author' => 1,
                'title' => 'Peking Duck',
                'description' => null,
                'cuisine' => 14,
                'type' => 1
            ],
            [
                'slug' => 'kung-pao-chicken',
                'variant' => null,
                'author' => 1,
                'title' => 'Kung Pao Chicken',
                'description' => null,
                'cuisine' => 14,
                'type' => 1
            ],
            [
                'slug' => 'butter-chicken',
                'variant' => null,
                'author' => 1,
                'title' => 'Butter Chicken',
                'description' => null,
                'cuisine' => 27,
                'type' => 1
            ],
            [
                'slug' => 'biryani',
                'variant' => null,
                'author' => 1,
                'title' => 'Biryani',
                'description' => null,
                'cuisine' => 27,
                'type' => 1
            ],
            [
                'slug' => 'samosas',
                'variant' => null,
                'author' => 1,
                'title' => 'Samosas',
                'description' => null,
                'cuisine' => 27,
                'type' => 1
            ],
            [
                'slug' => 'naan',
                'variant' => null,
                'author' => 1,
                'title' => 'Naan',
                'description' => null,
                'cuisine' => 27,
                'type' => 1
            ],
            [
                'slug' => 'tikka-masala',
                'variant' => null,
                'author' => 1,
                'title' => 'Tikka Masala',
                'description' => null,
                'cuisine' => 27,
                'type' => 1
            ],
            [
                'slug' => 'taco',
                'variant' => null,
                'author' => 1,
                'title' => 'Taco',
                'description' => null,
                'cuisine' => 41,
                'type' => 1
            ],
            [
                'slug' => 'buritto',
                'variant' => null,
                'author' => 1,
                'title' => 'Burrito',
                'description' => null,
                'cuisine' => 41,
                'type' => 1
            ],
            [
                'slug' => 'enchilada',
                'variant' => null,
                'author' => 1,
                'title' => 'Enchilada',
                'description' => null,
                'cuisine' => 41,
                'type' => 1
            ],
            [
                'slug' => 'guacamole',
                'variant' => null,
                'author' => 1,
                'title' => 'Guacamole',
                'description' => null,
                'cuisine' => 41,
                'type' => 1
            ],
            [
                'slug' => 'mexican-churros',
                'variant' => null,
                'author' => 1,
                'title' => 'Mexican Churros',
                'description' => null,
                'cuisine' => 41,
                'type' => 1
            ],
            [
                'slug' => 'sushi',
                'variant' => null,
                'author' => 1,
                'title' => 'Sushi',
                'description' => null,
                'cuisine' => 53,
                'type' => 17
            ],
            [
                'slug' => 'sashimi',
                'variant' => null,
                'author' => 1,
                'title' => 'Sashimi',
                'description' => null,
                'cuisine' => 53,
                'type' => 17
            ],
            [
                'slug' => 'ramen',
                'variant' => null,
                'author' => 1,
                'title' => 'Ramen',
                'description' => null,
                'cuisine' => 53,
                'type' => 15
            ],
            [
                'slug' => 'tempura',
                'variant' => null,
                'author' => 1,
                'title' => 'Tempura',
                'description' => null,
                'cuisine' => 53,
                'type' => 1
            ],
            [
                'slug' => 'miso',
                'variant' => null,
                'author' => 1,
                'title' => 'Miso',
                'description' => null,
                'cuisine' => 53,
                'type' => 15
            ],
            [
                'slug' => 'burger',
                'variant' => null,
                'author' => 1,
                'title' => 'Burger',
                'description' => null,
                'cuisine' => 54,
                'type' => 18
            ],
            [
                'slug' => 'bbq-ribs',
                'variant' => null,
                'author' => 1,
                'title' => 'BBQ Ribs',
                'description' => null,
                'cuisine' => 54,
                'type' => 1
            ],
            [
                'slug' => 'apple-pie',
                'variant' => null,
                'author' => 1,
                'title' => 'Apple Pie',
                'description' => null,
                'cuisine' => 54,
                'type' => 6
            ],
            [
                'slug' => 'coq-au-vin',
                'variant' => null,
                'author' => 1,
                'title' => 'Coq Au Vin',
                'description' => null,
                'cuisine' => 55,
                'type' => 14
            ],
            [
                'slug' => 'creme-brulee',
                'variant' => null,
                'author' => 1,
                'title' => 'Creme Brulee',
                'description' => null,
                'cuisine' => 55,
                'type' => 6
            ],
            [
                'slug' => 'pad-thai',
                'variant' => null,
                'author' => 1,
                'title' => 'Pad Thai',
                'description' => null,
                'cuisine' => 64,
                'type' => 14
            ],
            [
                'slug' => 'green-curry',
                'variant' => null,
                'author' => 1,
                'title' => 'Green Curry',
                'description' => null,
                'cuisine' => 64,
                'type' => 14
            ],
            [
                'slug' => 'tom-yum',
                'variant' => null,
                'author' => 1,
                'title' => 'Tom Yum',
                'description' => null,
                'cuisine' => 64,
                'type' => 15
            ],
            [
                'slug' => 'mango-sticky-rice',
                'variant' => null,
                'author' => 1,
                'title' => 'Mango Sticky Rice',
                'description' => null,
                'cuisine' => 64,
                'type' => 14
            ],
            [
                'slug' => 'paella',
                'variant' => null,
                'author' => 1,
                'title' => 'Paella',
                'description' => null,
                'cuisine' => 69,
                'type' => 14
            ],
            [
                'slug' => 'churros',
                'variant' => null,
                'author' => 1,
                'title' => 'Churros',
                'description' => null,
                'cuisine' => 69,
                'type' => 2
            ],
            [
                'slug' => 'gazpacho',
                'variant' => null,
                'author' => 1,
                'title' => 'Gazpacho',
                'description' => null,
                'cuisine' => 69,
                'type' => 14
            ],
            [
                'slug' => 'jamon-iberico',
                'variant' => null,
                'author' => 1,
                'title' => 'Jamon Iberico',
                'description' => null,
                'cuisine' => 69,
                'type' => 14
            ],
            [
                'slug' => 'hummus',
                'variant' => null,
                'author' => 1,
                'title' => 'Hummus',
                'description' => null,
                'cuisine' => 76,
                'type' => 16
            ],
            [
                'slug' => 'falafel',
                'variant' => null,
                'author' => 1,
                'title' => 'Falafel',
                'description' => null,
                'cuisine' => 76,
                'type' => 14
            ],
            [
                'slug' => 'tabouleh',
                'variant' => null,
                'author' => 1,
                'title' => 'Tabbouleh',
                'description' => null,
                'cuisine' => 76,
                'type' => 14
            ],
            [
                'slug' => 'kimchi',
                'variant' => null,
                'author' => 1,
                'title' => 'Kimchi',
                'description' => null,
                'cuisine' => 83,
                'type' => 22
            ],
            [
                'slug' => 'bulgogi',
                'variant' => null,
                'author' => 1,
                'title' => 'Bulgogi',
                'description' => null,
                'cuisine' => 83,
                'type' => 14
            ],
            [
                'slug' => 'bibimbap',
                'variant' => null,
                'author' => 1,
                'title' => 'Bibimbap',
                'description' => null,
                'cuisine' => 83,
                'type' => 14
            ],
            [
                'slug' => 'tteokbokki',
                'variant' => null,
                'author' => 1,
                'title' => 'Tteokbokki',
                'description' => null,
                'cuisine' => 83,
                'type' => 14
            ],
            [
                'slug' => 'moussaka',
                'variant' => null,
                'author' => 1,
                'title' => 'Moussaka',
                'description' => null,
                'cuisine' => 88,
                'type' => 14
            ],
            [
                'slug' => 'souvlaki',
                'variant' => null,
                'author' => 1,
                'title' => 'Souvlaki',
                'description' => null,
                'cuisine' => 88,
                'type' => 14
            ],
            [
                'slug' => 'tzatziki',
                'variant' => null,
                'author' => 1,
                'title' => 'Tzatziki',
                'description' => null,
                'cuisine' => 88,
                'type' => 12
            ],
            [
                'slug' => 'baklava',
                'variant' => null,
                'author' => 1,
                'title' => 'Baklava',
                'description' => null,
                'cuisine' => 88,
                'type' => 23
            ],
            [
                'slug' => 'greek-salad',
                'variant' => null,
                'author' => 1,
                'title' => 'Greek Salad',
                'description' => null,
                'cuisine' => 88,
                'type' => 10
            ],
            [
                'slug' => 'shawarma',
                'variant' => null,
                'author' => 1,
                'title' => 'Shawarma',
                'description' => null,
                'cuisine' => 94,
                'type' => 18
            ],
            [
                'slug' => 'kebab',
                'variant' => null,
                'author' => 1,
                'title' => 'Kebab',
                'description' => null,
                'cuisine' => 94,
                'type' => 18
            ],
            [
                'slug' => 'pho',
                'variant' => null,
                'author' => 1,
                'title' => 'Pho',
                'description' => null,
                'cuisine' => 100,
                'type' => 15
            ],
            [
                'slug' => 'banh-mi',
                'variant' => null,
                'author' => 1,
                'title' => 'Banh Mi',
                'description' => null,
                'cuisine' => 100,
                'type' => 18
            ],
            [
                'slug' => 'spring-rolls',
                'variant' => null,
                'author' => 1,
                'title' => 'Spring Rolls',
                'description' => null,
                'cuisine' => 100,
                'type' => 11
            ],
            [
                'slug' => 'bun-cha',
                'variant' => null,
                'author' => 1,
                'title' => 'Bun Cha',
                'description' => null,
                'cuisine' => 100,
                'type' => 14
            ],
            [
                'slug' => 'baba-ganoush',
                'variant' => null,
                'author' => 1,
                'title' => 'Baba Ganoush',
                'description' => null,
                'cuisine' => 104,
                'type' => 13
            ],
            [
                'slug' => 'fattoush',
                'variant' => null,
                'author' => 1,
                'title' => 'Fattoush',
                'description' => null,
                'cuisine' => 104,
                'type' => 10
            ],
            [
                'slug' => 'risotto-lombardy',
                'variant' => 4,
                'author' => 1,
                'title' => 'Risotto',
                'description' => 'From Lombardy',
                'cuisine' => 2,
                'type' => 14
            ],
            [
                'slug' => 'ossobuco',
                'variant' => null,
                'author' => 1,
                'title' => 'Ossobuco',
                'description' => 'Braised veal shanks',
                'cuisine' => 2,
                'type' => 24
            ],
            [
                'slug' => 'polenta',
                'variant' => null,
                'author' => 1,
                'title' => 'Polenta',
                'description' => 'Cornmeal dish',
                'cuisine' => 2,
                'type' => 11
            ],
            [
                'slug' => 'panna-cotta',
                'variant' => null,
                'author' => 1,
                'title' => 'Panna Cotta',
                'description' => 'desert',
                'cuisine' => 2,
                'type' => 6
            ],
            [
                'slug' => 'agnolotti',
                'variant' => null,
                'author' => 1,
                'title' => 'Agnolotti',
                'description' => 'Stuffed pasta from Piedmont',
                'cuisine' => 2,
                'type' => 14
            ],
            [
                'slug' => 'margherita',
                'variant' => 1,
                'author' => 1,
                'title' => 'Pizza Margherita',
                'description' => 'From Naples',
                'cuisine' => 3,
                'type' => 14
            ],
            [
                'slug' => 'pasta-alla-norma',
                'variant' => 2,
                'author' => 1,
                'title' => 'Pasta alla Norma',
                'description' => 'From Sicily',
                'cuisine' => 3,
                'type' => 14
            ],
            [
                'slug' => 'caponata',
                'variant' => null,
                'author' => 1,
                'title' => 'Caponata',
                'description' => 'Sicilian eggplant dish',
                'cuisine' => 3,
                'type' => 11
            ],
            [
                'slug' => 'parmigiana-di-melanzane',
                'variant' => null,
                'author' => 1,
                'title' => 'Parmigiana di Melanzane',
                'description' => 'Eggplant Parmesan',
                'cuisine' => 3,
                'type' => 14
            ],
            [
                'slug' => 'focaccia',
                'variant' => null,
                'author' => 1,
                'title' => 'Focaccia',
                'description' => 'Flatbread',
                'cuisine' => 3,
                'type' => 26
            ],
        ]);
    }
}
