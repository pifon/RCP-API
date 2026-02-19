<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'yeast',
                'slug' => 'yeast-dry',
                'description' => 'a form of yeast—single-celled fungi—that has been dehydrated to extend shelf life and simplify storage. It’s commonly used in baking to ferment dough, producing carbon dioxide that makes bread rise. There are two main types: active dry yeast, which needs to be dissolved in warm water before use, and instant yeast, which can be mixed directly into dry ingredients.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 2,
                'name' => 'water',
                'slug' => 'water',
                'description' => 'clear, tasteless, odorless, and essential liquid composed of two hydrogen atoms bonded to one oxygen atom (H₂O). It’s vital for all known forms of life, used for drinking, cooking, cleaning, and countless other daily activities. Pure water contains no calories, nutrients, or allergens.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 3,
                'name' => 'white bread flour',
                'slug' => 'white-bread-flour',
                'description' => 'a refined wheat flour with a high protein (gluten) content, typically 11–13%, which gives bread its chewy texture and helps dough rise. It’s made from the wheat endosperm after removing the bran and germ, resulting in a pale, fine flour ideal for baking yeast breads like sandwich loaves, rolls, and pizza dough.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 0,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 4,
                'name' => 'salt',
                'slug' => 'salt',
                'description' => 'Kitchen salt, or table salt, is a fine-grained mineral primarily made of sodium chloride. It’s used to season and preserve food. Often, it’s fortified with iodine to help prevent iodine deficiency and may contain anti-caking agents to keep it free-flowing. It’s an essential staple in kitchens worldwide.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 5,
                'name' => 'extra virgin olive oil',
                'slug' => 'extra-virgin-olive-oil',
                'description' => 'the highest-quality olive oil, made by cold-pressing fresh olives without using heat or chemicals. It has a rich, fruity flavor and a vibrant green-golden color. EVOO is prized for its health benefits, including antioxidants and healthy fats, and is commonly used for dressings, drizzling, and finishing dishes rather than high-heat cooking.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 0,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 6,
                'name' => 'cherry tomatoes',
                'slug' => 'tomatoes-cherry',
                'description' => 'small, round, and sweet varieties of the tomato plant, typically 1–2 cm in diameter. They have thin skins, juicy flesh, and a bright, tangy flavor, making them popular for salads, snacking, and garnishes. Available in red, yellow, orange, and other colors, they are eaten fresh or lightly cooked.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 7,
                'name' => 'red onion',
                'slug' => 'onion-red',
                'description' => 'a variety of onion with purplish-red skin and white flesh tinged with red. It has a mild to sweet flavor when raw and becomes sweeter when cooked. Often used in salads, sandwiches, and salsas for its color and crisp texture, it can also be grilled, roasted, or pickled.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
            [
                'id' => 8,
                'name' => 'rosemary',
                'slug' => 'rosemary',
                'description' => 'a fragrant, evergreen herb with needle-like leaves, native to the Mediterranean region. It has a piney, slightly peppery flavor with hints of citrus and is commonly used to season meats, breads, and roasted vegetables. In addition to culinary uses, it’s valued for its aromatic and ornamental qualities.',
                'vegan' => 1,
                'vegetarian' => 1,
                'halal' => 1,
                'kosher' => 1,
                'created_at' => '2025-07-09 13:41:59',
                'updated_at' => '2025-07-09 13:41:59',
            ],
        ]);
    }
}
