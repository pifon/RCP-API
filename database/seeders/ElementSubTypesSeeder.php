<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElementSubTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('element_sub_types')->insert([
            [
                'name' => 'monosaccharide',
                'type' => 2,
                'description' => 'sugar',
                'ref' => null,
            ], [
                'name' => 'disaccharide',
                'type' => 2,
                'description' => 'sugar',
                'ref' => null,
            ], [
                'name' => 'oligosaccharide',
                'type' => 2,
                'description' => 'sugar',
                'ref' => null,
            ], [
                'name' => 'polysaccharide',
                'type' => 2,
                'description' => 'sugar',
                'ref' => null,
            ], [
                'name' => 'saturated',
                'type' => 4,
                'description' => 'Often called “solid” fats, saturated fats may be visible, floating near the top of foods like meat stews or yogurts. Less visible but still present saturated fats are found in many foods, including cheeses, fatty meats, whole-fat milk, ice cream and butter. Primarily, saturated fats are found in animal products. They get their name because the fat\'s carbon atoms are highly saturated with hydrogen. Diets high in these foods are linked to an increased risk of chronic diseases, especially coronary artery disease.',
                'ref' => null,
            ], [
                'name' => 'monounsaturated',
                'type' => 4,
                'description' => 'Fats that only have a single double-bonded carbon atom are known as monounsaturated fats. Liquid at room temperature, monounsaturated fats are found in foods like olive oil, sesame oil, avocados, nuts, peanut butter and sunflower oil. Eaten in moderation, monounsaturated fats can actually be beneficial for health. These fats can reduce the risk of stroke and heart disease, lower bad cholesterol levels and provide high levels of vitamin E, an important antioxidant.',
                'ref' => null,
            ], [
                'name' => 'polyunsaturated',
                'type' => 4,
                'description' => 'Fats with at least two double-bonded carbons are known as polyunsaturated fats. Good sources of these fats include safflower oil, corn oil, soybean oil and fatty fish like trout, salmon, herring and mackerel. When eaten moderately in place of saturated fats, polyunsaturated fats can be a healthy choice. They can lower the risk of heart disease and also reduce cholesterol levels in the blood. This classification of fats includes essential fats that cannot be produced by the body, like omega-3 and omega-6 fatty acids. The only way to get these essential fats is from your diet. Omega-3 and omega-6 are essential for brain function and healthy body development and growth.',
                'ref' => null,
            ], [
                'name' => 'trans',
                'type' => 4,
                'description' => 'Trans Fatty Acids. Processed foods are often loaded with trans fatty acids, by-products of a chemical process known as hydrogenation. Foods high in these harmful fats include animal products, cooking oils, margarine, shortening and foods that contain these ingredients. Trans fatty acids raise bad cholesterol levels, lower good cholesterol levels and increase the risk of developing cardiovascular disease.',
                'ref' => null,
            ], [
                'name' => 'oils',
                'type' => 4,
                'description' => 'Oils are part of a well-balanced diet, but not all oils are healthy. Oils high in polyunsaturated fatty acids contribute to good health because they are high in omega-3 and omega-6 fatty acids. These include hemp, flax, grape seed, safflower, sunflower, soybean, cottonseed and sesame oils.',
                'ref' => null,
            ], [
                'name' => 'long-chain saturated',
                'type' => 4,
                'description' => 'Fatty acids are the basic building blocks of lipids or fats. One of the ways fatty acids are classified is by the number of carbon atoms in their tails. Long-chain fatty acids are those with 14 or more carbons.',
                'ref' => null,
            ], [
                'name' => 'fat-soluble',
                'type' => 6,
                'description' => 'Four of the essential vitamins are classified as fat-soluble vitamins. Fat-soluble vitamins are found in fatty foods, such as vegetables oils, and the fatty components of meat, poultry, seafood, dairy products, grains, nuts, seeds and some fruits and vegetables. The fat-soluble vitamins include vitamins A, D, E and K. Fat-soluble vitamins that are not used right away are stored in the fatty tissue of your body. For this reason, they do not necessarily need to be replenished every day, but it is also easier to overdose on fat-soluble vitamins than on water-soluble vitamins. This is particularly true if you are taking vitamin supplements.',
                'ref' => null,
            ], [
                'name' => 'water-soluble',
                'type' => 6,
                'description' => 'Nine of the 13 essential vitamins are classified as water-soluble vitamins. That means they are found in great abundance in watery foods such as fruits and vegetables and in the watery components of grains, nuts, seeds and animal products. The water-soluble vitamins include A in the form of beta-carotene, B6, B12, riboflavin, thiamin, niacin, folate, biotin, and C. Water-soluble vitamins are used immediately by your body or they are excreted in your urine. Unlike fat-soluble vitamins, they cannot be stored in your body so they should be replenished on a daily basis.',
                'ref' => null,
            ], [
                'name' => 'major',
                'type' => 5,
                'description' => 'Minerals needed in relatively large amounts',
                'ref' => null,
            ], [
                'name' => 'trace',
                'type' => 5,
                'description' => 'Minerals necessary in relatively small, trace amounts',
                'ref' => null,
            ], [
                'name' => 'soluble',
                'type' => 7,
                'description' => 'This type of fiber dissolves in water to form a gel-like material. It can help lower blood cholesterol and glucose levels. Soluble fiber is found in oats, peas, beans, apples, citrus fruits, carrots, barley and psyllium.',
                'ref' => null,
            ], [
                'name' => 'insoluble',
                'type' => 7,
                'description' => 'This type of fiber promotes the movement of material through your digestive system and increases stool bulk, so it can be of benefit to those who struggle with constipation or irregular stools. Whole-wheat flour, wheat bran, nuts, beans and vegetables, such as cauliflower, green beans and potatoes, are good sources of insoluble fiber.',
                'ref' => null,
            ], [
                'name' => 'standard',
                'type' => 3,
                'description' => 'The amino acids that an organism can synthesize on its own are referred to as standard amino acids',
                'ref' => null,
            ], [
                'name' => 'essential',
                'type' => 3,
                'description' => 'The amino acids that an organism cannot synthesize on its own are referred to as essential amino acids',
                'ref' => null,
            ], [
                'name' => 'semi-essential',
                'type' => 3,
                'description' => 'protein',
                'ref' => null,
            ],
        ]);
    }
}
