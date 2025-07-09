<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_groups')->insert([
            [
                'name' => 'vegan',
                'description' => 'Vegan product rules are guidelines that ensure a product is free from animal-derived ingredients and is not tested on animals.',
                'ref' => 'https://en.wikipedia.org/wiki/Veganism',
            ],
            [
                'name' => 'vegetarian',
                'description' => 'Vegetarian products are those that exclude meat, poultry, fish, and seafood but may include other animal-derived products such as dairy, eggs, or honey. The rules for vegetarian products vary slightly depending on the type of vegetarianism, but the core principle is to avoid animal slaughter.',
                'ref' => 'https://en.wikipedia.org/wiki/Vegetarianism',
            ],
            [
                'name' => 'halal',
                'description' => 'Halal food refers to food and beverages that are permissible for Muslims to consume according to Islamic law (Sharia). The term "halal" means "lawful" or "permissible" in Arabic. For food to be considered halal, it must comply with various dietary rules outlined in the Quran, the Hadith (sayings of the Prophet Muhammad), and the interpretations of Islamic scholars.',
                'ref' => 'https://en.wikipedia.org/wiki/Islamic_dietary_laws',
            ],
            [
                'name' => 'kosher',
                'description' => 'Kosher foods are foods that conform to the Jewish dietary regulations of kashrut (dietary law). The laws of kashrut apply to food derived from living creatures and kosher foods are restricted to certain types of mammals, birds and fish meeting specific criteria; the flesh of any animals that do not meet these criteria is forbidden by the dietary laws. Furthermore, kosher mammals and birds must be slaughtered according to a process known as shechita and their blood may never be consumed and must be removed from the meat by a process of salting and soaking in water for the meat to be permissible for use. All plant-based products, including fruits, vegetables, grains, herbs and spices, are intrinsically kosher, although certain produce grown in the Land of Israel is subjected to other requirements, such as tithing, before it may be consumed.',
                'ref' => 'https://en.wikipedia.org/wiki/Kosher_foods',
            ],
            [
                'name' => 'gluten-free',
                'description' => 'A gluten-free product is any food or beverage that does not contain gluten, a protein found in certain grains, specifically wheat, barley, rye, and their derivatives. Gluten is commonly found in many baked goods, pastas, cereals, and processed foods, and can cause adverse health effects in people with celiac disease or those with non-celiac gluten sensitivity.',
                'ref' => null,
            ],
            [
                'name' => 'diary-free',
                'description' => 'Dairy-free products are foods and beverages that do not contain any form of dairy or dairy-derived ingredients. Dairy is derived from animal milk, primarily from cows, goats, and sheep, and includes products such as milk, cheese, butter, yogurt, cream, and other milk-based ingredients. Dairy-free products are essential for individuals who are lactose intolerant, have a milk allergy, or follow a vegan diet.',
                'ref' => null,
            ],
            [
                'name' => 'meat',
                'description' => 'Meat (non poultry)',
                'ref' => null,
            ],
            [
                'name' => 'poultry',
                'description' => 'Poultry refers to domesticated birds that are raised for their meat, eggs, or feathers.',
                'ref' => null,
            ],
            [
                'name' => 'fish',
                'description' => 'Fish are aquatic vertebrates (with a backbone) that are typically covered in scales and have gills for breathing underwater. Examples: Salmon, Tuna, Cod, Trout, Mackerel.',
                'ref' => null,
            ],
            [
                'name' => 'shellfish',
                'description' => 'Shellfish refers to aquatic animals with an exoskeleton or shell, which can be either crustaceans or mollusks (described further below). Examples: Shrimp, Lobster, Crab (crustaceans), Clams, Oysters, Mussels (mollusks).',
                'ref' => null,
            ],
            [
                'name' => 'fruit',
                'description' => 'Fruts',
                'ref' => null,
            ],
            [
                'name' => 'vegetable',
                'description' => 'Vegetables',
                'ref' => null,
            ],
            [
                'name' => 'nut',
                'description' => 'Nuts',
                'ref' => null,
            ],
            [
                'name' => 'grain',
                'description' => 'Grains and flours',
                'ref' => null,
            ],
            [
                'name' => 'mushroom',
                'description' => 'Mushrooms',
                'ref' => null,
            ],
            [
                'name' => 'diary',
                'description' => 'Milk and milk derivatives',
                'ref' => null,
            ],
            [
                'name' => 'bread',
                'description' => 'Bakery products',
                'ref' => null,
            ],
            [
                'name' => 'legumes',
                'description' => 'Legumes are plants that produce seeds in pods. This group includes beans, lentils, peas, and chickpeas. Examples: Black beans, Kidney beans, Chickpeas, Lentils, Peas.',
                'ref' => null,
            ],
            [
                'name' => 'fats',
                'description' => 'This group includes fats, and fat-containing foods used in cooking and for adding flavor and texture obtained from animals. Examples: Butter, lard, goose fat etc.',
                'ref' => null,
            ],
            [
                'name' => 'olives',
                'description' => 'This group includes fats, oils, and fat-containing foods used in cooking and for adding flavor and texture. Only Plant based. Examples: Olive oil, Coconut oil, Avocados, Peanut butter.',
                'ref' => null,
            ],
            [
                'name' => 'sweets',
                'description' => 'Foods that are primarily sweetened with sugar or other sweeteners, often including candies, chocolates, and pastries. Examples: Candy, Chocolate, Cake, Cookies, Ice cream.',
                'ref' => null,
            ],
            [
                'name' => 'non-alcoholic',
                'description' => 'Drinks that include only non-alcoholic beverages. Examples: Coffee, Tea, Fruit juice, Soda,',
                'ref' => null,
            ],
            [
                'name' => 'alcoholic',
                'description' => 'Drinks that include only alcoholic beverages. Examples: beer, wine, spirits.',
                'ref' => null,
            ],
            [
                'name' => 'herbs',
                'description' => 'Spices and herbs are plant-derived seasonings used to add flavor, aroma, and color to food. Examples: Cilantro, Basil, Thyme, Cumin, Turmeric, Black pepper.',
                'ref' => null,
            ],
            [
                'name' => 'spices',
                'description' => 'Spices and herbs are plant-derived seasonings used to add flavor, aroma, and color to food. Examples: Cilantro, Basil, Thyme, Cumin, Turmeric, Black pepper.',
                'ref' => null,
            ],
            [
                'name' => 'fermented',
                'description' => 'Foods that have undergone fermentation, a process where microorganisms break down food components, often improving digestibility or preserving the food. Examples: Kimchi, Sauerkraut, Kefir, Yogurt, Miso, Sauerkraut, Pickles.',
                'ref' => null,
            ],
            [
                'name' => 'seaweed',
                'description' => 'Edible plants that grow in marine environments, often used in Asian cuisine. Examples: Nori, Wakame, Kelp, Dulse.',
                'ref' => null,
            ],
            [
                'name' => 'dairy-alternative',
                'description' => 'Plant-based substitutes for dairy products, often used by people who follow vegan or lactose-free diets. Examples: Almond milk, Soy milk, Coconut yogurt, Vegan cheese.',
                'ref' => null,
            ],
            [
                'name' => 'protein alternatives',
                'description' => 'Non-animal protein sources that are often used as meat substitutes in plant-based diets. Examples: Tofu, Tempeh, Seitan, Plant-based burgers (made from soy, pea protein, etc.).',
                'ref' => null,
            ],
            [
                'name' => 'processed',
                'description' => 'This category includes foods that have been altered through processing, often to extend shelf life or enhance flavor. Examples: Chips, Canned goods, Frozen meals, Packaged snacks, Processed meats.',
                'ref' => null,
            ],
        ]);
    }
}
