<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElementTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('element_types')->insert([
            'name' => 'water',
            'description' => 'Water is defined as an essential nutrient because it is required in amounts that exceed the body\'s ability to produce it. All biochemical reactions occur in water. It fills the spaces in and between cells and helps form structures of large molecules such as protein and glycogen.',
            'ref' => 'https://en.wikipedia.org/wiki/Water',
        ],[
            'name' => 'carbohydrate',
            'description' => 'Carbohydrates, also known as saccharides or carbs, are sugars or starches. They are a major food source and a key form of energy for most organisms. They consist of carbon, hydrogen, and oxygen atoms.',
            'ref' => 'https://en.wikipedia.org/wiki/Carbohydrate',
        ],[
            'name' => 'protein',
            'description' => 'Protein is an important component of every cell in the body. Hair and nails are mostly made of protein. Your body uses protein to build and repair tissues. You also use protein to make enzymes, hormones, and other body chemicals. Protein is an important building block of bones, muscles, cartilage, skin, and blood.',
            'ref' => 'https://en.wikipedia.org/wiki/Protein',
        ],[
            'name' => 'lipid',
            'description' => 'Lipids are molecules that contain hydrocarbons and make up the building blocks of the structure and function of living cells. Examples of lipids include fats, oils, waxes, certain vitamins, hormones and most of the non-protein membrane of cells.',
            'ref' => 'https://en.wikipedia.org/wiki/Lipid',
        ],[
            'name' => 'mineral',
            'description' => 'As a group, minerals are one of the four groups of essential nutrients, the others of which are vitamins, essential fatty acids, and essential amino acids. The five major minerals in the human body are calcium, phosphorus, potassium, sodium, and magnesium.',
            'ref' => 'https://en.wikipedia.org/wiki/Mineral_(nutrient)',
        ],[
            'name' => 'vitamin',
            'description' => 'A vitamin is an organic molecule which is an essential micronutrient, that an organism needs in small quantities for the proper functioning of its metabolism.',
            'ref' => 'https://en.wikipedia.org/wiki/Vitamin',
        ],[
            'name' => 'fibre',
            'description' => 'Fibre is an essential part of a healthy diet. It helps to keep your digestive system in good working order, and has many other important health benefits. Fibre is found in many plant-based, carbohydrate-rich foods, such as wholemeal bread, fruit and vegetables and pulses. Dietary fibre is a term that is used for plant-based carbohydrates that, unlike other carbohydrates (such as sugars and starch), are not digested in the small intestine. It also includes other plant components like lignin.',
            'ref' => 'https://en.wikipedia.org/wiki/Dietary_fiber',
        ]);
    }
}

