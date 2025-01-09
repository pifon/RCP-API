<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('cuisines')->insert([
            [
                'name' => 'Italian',
                'variant' => null,
                'description' => 'Simple, high-quality ingredients like olive oil, tomatoes, and fresh herbs; comforting flavors; and global adaptability.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Northern',
                'description' => 'Known for its rich and creamy sauces, use of butter, rice, and polenta, with a preference for meats like pork, beef, and poultry.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Southern',
                'description' => 'Uses more olive oil, tomatoes, and fresh vegetables, with a focus on seafood, pasta, and lighter, spicier flavors.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Central',
                'description' => 'Combines elements from both the north and south, focusing on pasta, meats (especially lamb), legumes, and cheeses like pecorino.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Sicilian',
                'description' => 'A mix of Mediterranean and Arab influences, with heavy use of citrus, eggplant, almonds, and seafood.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Neapolitan',
                'description' => 'Known for its pizza and seafood, with an emphasis on fresh, simple ingredients like tomatoes, mozzarella, and basil.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Roman',
                'description' => 'Characterized by simplicity and rustic flavors, often focusing on pasta, artichokes, and offal (organ meats).',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Venetian',
                'description' => 'Influenced by its proximity to the sea, Venetian cuisine features a lot of seafood and risotto, with a sweet and sour flavor profile.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Emilia-Romagna',
                'description' => 'Known for rich meats, cheeses (like Parmigiano-Reggiano), and homemade pasta. It is considered the food capital of Italy.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Apulian',
                'description' => 'A coastal region known for olive oil, fresh seafood, and pasta made with durum wheat.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Trentino-Alto Adige',
                'description' => 'Combines Italian and Austrian influences, with hearty meat dishes, dumplings, and apple-based desserts.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Ligurian',
                'description' => 'Famous for its use of fresh herbs, olive oil, and seafood, especially in pasta dishes.',
            ],
            [
                'name' => 'Italian',
                'variant' => 'Calabrian',
                'description' => 'Known for its spicy flavors, heavy use of chili peppers, and simple, rustic dishes.',
            ],
            [
                'name' => 'Chinese',
                'variant' => null,
                'description' => 'Diverse regional styles (Cantonese, Sichuan, Hunan, etc.), bold flavors, and a balance of sweet, savory, sour, and spicy.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Cantonese',
                'description' => 'Yue. Known for its delicate flavors, freshness, and emphasis on steaming, stir-frying, and braising. Cantonese cuisine often uses a variety of ingredients such as seafood, poultry, and vegetables.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Sichuan',
                'description' => 'Chuan. Famous for its bold, spicy, and flavorful dishes, Sichuan cuisine uses a lot of chili peppers and Sichuan peppercorns, which add a numbing effect (málà) along with heat.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Shandong',
                'description' => 'Lu. Known for its use of fresh ingredients, including seafood, and cooking techniques like frying, steaming, and braising. It emphasizes clear broths and savory flavors.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Jiangsu',
                'description' => 'Su. Known for its delicate, slightly sweet flavors, Jiangsu cuisine emphasizes fresh, high-quality ingredients, including seafood, poultry, and meats, with a focus on soups and stews.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Zhejiang',
                'description' => 'Zhe. Zhejiang cuisine is known for its light, fresh flavors and emphasis on seafood, poultry, and vegetables. Dishes are often braised or stir-fried.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Fujian',
                'description' => 'Min. Known for its light, umami flavors, and emphasis on broths and soups, Fujian cuisine uses a lot of seafood, especially shellfish, and incorporates complex combinations of flavors.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Hunan',
                'description' => 'Xiang. Hunan cuisine is known for its fiery hot, sour, and spicy flavors, similar to Sichuan, but with more emphasis on fresh chili peppers and a greater variety of pickled ingredients.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Anhui',
                'description' => 'Hui. Known for its use of wild herbs, mushrooms, and other foraged ingredients, Anhui cuisine often features braising and stewing to highlight natural flavors.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Xinjiang',
                'description' => 'Influenced by Central Asian flavors, Xinjiang cuisine is known for its use of lamb, beef, bread, and an array of spices. It is characterized by hearty, flavorful dishes often involving grilling or skewering meats. ',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Tibetan',
                'description' => 'Influenced by the high-altitude environment, Tibetan cuisine includes hearty, simple dishes that often feature barley, meat, dairy, and root vegetables. It is a blend of Tibetan, Nepalese, and Indian flavors.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Hong Kong',
                'description' => 'Known for its fusion of Cantonese cooking with influences from Western and other Asian cuisines. It is famous for dim sum, roasted meats, and seafood.',
            ],
            [
                'name' => 'Chinese',
                'variant' => 'Taiwanese',
                'description' => 'Combining elements from mainland Chinese cuisines with unique local flavors, Taiwanese cuisine features a variety of street foods, noodle dishes, and fresh ingredients.',
            ],
            [
                'name' => 'Indian',
                'variant' => null,
                'description' => 'Rich use of spices, vegetarian options, and hearty, flavorful dishes that appeal to a wide audience.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'North',
                'description' => 'North Indian cuisine is known for its rich, hearty dishes, the use of dairy products (like paneer, yogurt, and ghee), and an emphasis on wheat-based bread (naan, roti). The food is often mildly spiced and tends to feature meat dishes, especially lamb and chicken.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'South',
                'description' => 'South Indian cuisine features rice as the staple, often paired with sambhar (lentil stew), and dosas (crispy rice crepes). It is known for its tangy, spicy flavors, use of coconut, and a variety of chutneys and pickles.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Western',
                'description' => 'Western India includes cuisine from Gujarat, Maharashtra, and Rajasthan. It is characterized by a wide range of vegetarian dishes (especially in Gujarat), seafood (especially in coastal regions like Goa), and rich, spicy curries from Rajasthan.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Eastern',
                'description' => 'Eastern Indian cuisine features rice, fish, and vegetables as staple ingredients. The flavors are often subtle and less spicy than other regional cuisines, with an emphasis on mustard oil and unique spices.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Punjabi',
                'description' => 'Known for its robust flavors, Punjabi cuisine uses a wide range of spices, and is often associated with tandoor cooking. Dairy products such as yogurt, butter, and paneer are heavily used in both vegetarian and meat-based dishes.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Kashmiri',
                'description' => 'Kashmiri cuisine is known for its rich and aromatic flavors, using dried fruits, yogurt, and Kashmiri saffron. The food is often slow-cooked, with an emphasis on meat dishes, especially lamb.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Mughlai',
                'description' => 'Mughlai cuisine blends Central Asian, Persian, and Indian influences, and is known for its rich gravies, meat dishes, and aromatic rice dishes. It also uses luxurious ingredients like saffron, dried fruits, and nuts.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Bengali',
                'description' => 'Bengali cuisine is characterized by the use of mustard oil, fish, rice, and sweets. It is known for its intricate, balanced flavors with a focus on both savory and sweet dishes.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Goan',
                'description' => 'Influenced by Portuguese colonialism, Goan cuisine features coconut, rice, seafood, and the use of vinegar and spices. It is known for its seafood dishes and tangy flavors.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Kerala',
                'description' => 'Kerala cuisine uses a lot of coconut, rice, seafood, and curry leaves. The food is often prepared using mild spices and is known for its balance of flavors. Kerala is famous for its vegetarian dishes and seafood curries.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Tamil',
                'description' => 'Tamil cuisine, mainly from Tamil Nadu, includes rice, sambar, dosas, and idlis as staples. It is characterized by the use of tamarind, curry leaves, and black pepper in a variety of vegetarian and non-vegetarian dishes.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Hyderabadi',
                'description' => 'Known for its flavorful biryanis, Hyderabadi cuisine is influenced by Persian and Mughlai styles. It features rich, spiced rice dishes, slow-cooked meats, and a variety of kebabs.',
            ],
            [
                'name' => 'Indian',
                'variant' => 'Rajasthani',
                'description' => 'Rajasthani cuisine is rich, spicy, and often vegetarian, using ingredients like lentils, wheat, and dairy. It is famous for its fried, rich dishes suited to the arid climate of the region.',
            ],
            [
                'name' => 'Mexican',
                'variant' => null,
                'description' => 'Bold flavors, fresh ingredients like avocados, tomatoes, and chili peppers, and its adaptability (e.g., Tex-Mex)',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Northern',
                'description' => 'Known for its meat-heavy dishes, especially beef and goat, and its use of wheat (for tortillas and bread) rather than corn. Grilled foods and dried meats are popular, reflecting the region\'s ranching culture.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Central',
                'description' => 'Features a mix of indigenous and colonial influences, with an emphasis on corn, beans, and chili peppers. Central Mexico is known for its street food, complex sauces, and traditional stews.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Southern',
                'description' => 'Rich in indigenous traditions, this region uses tropical ingredients like plantains, cacao, and fresh herbs. The food is often spicier and includes more vegetarian options.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Oaxaca',
                'description' => 'Oaxaca, known as the "Land of the Seven Moles," is celebrated for its diverse mole sauces, use of corn, and indigenous Zapotec and Mixtec influences.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Yucatán',
                'description' => 'Influenced by Mayan culture, Yucatán cuisine features unique spices and cooking techniques like pit-roasting. It often uses citrus flavors and ingredients like annatto (achiote).',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Baja California',
                'description' => 'Influenced by proximity to the U.S. and its coastal location, Baja cuisine blends Mexican and international flavors, emphasizing fresh seafood.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Veracruz',
                'description' => 'Known for its Afro-Caribbean influences and coastal flavors, Veracruz cuisine features seafood, tropical fruits, and herbs like epazote.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Western',
                'description' => 'Jalisco and Michoacán. Known for its traditional soups, stews, and tequila-based dishes. Western Mexico offers some of the country\'s most famous cultural and culinary exports.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Puebla',
                'description' => 'Puebla is the birthplace of some of Mexico\'s most iconic dishes, including mole and chiles en nogada. Its cuisine reflects a mix of indigenous and Spanish colonial influences.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Chiapas',
                'description' => 'Chiapas cuisine features indigenous flavors, simple cooking methods, and the use of tropical ingredients. It is less spicy compared to other regions.',
            ],
            [
                'name' => 'Mexican',
                'variant' => 'Indigenous',
                'description' => 'Indigenous Mexican cuisine focuses on pre-Columbian ingredients like maize, beans, squash, chili, cacao, and native herbs. It often excludes European ingredients like wheat, dairy, and pork.',
            ],
            [
                'name' => 'Japanese',
                'variant' => null,
                'description' => 'Minimalistic presentation, emphasis on fresh ingredients, and unique flavors like umami.',
            ],
            [
                'name' => 'American',
                'variant' => null,
                'description' => 'Iconic comfort food, fast food influence, and globalized dishes like burgers and fries.',
            ],
            [
                'name' => 'French',
                'variant' => null,
                'description' => 'Known for elegance, sophistication, and techniques that form the basis of modern Western cooking.',
            ],
            [
                'name' => 'French',
                'variant' => 'Provence',
                'description' => 'Mediterranean Influence. Olive oil, herbs, and fresh vegetables dominate.',
            ],
            [
                'name' => 'French',
                'variant' => 'Alsace-Lorraine',
                'description' => 'German Influence. Rich, hearty dishes with German flavors.',
            ],
            [
                'name' => 'French',
                'variant' => 'Burgundy',
                'description' => 'Wine Country. Focuses on dishes made with wine and rich sauces.',
            ],
            [
                'name' => 'French',
                'variant' => 'Normandy',
                'description' => 'Coastal Influence. Cream, butter, apples, and seafood are key ingredients.',
            ],
            [
                'name' => 'French',
                'variant' => 'Brittany',
                'description' => 'Coastal Influence. Known for seafood and crepes.',
            ],
            [
                'name' => 'French',
                'variant' => 'Lyonnaise',
                'description' => 'Rich, traditional dishes from the gastronomic capital of France.',
            ],
            [
                'name' => 'French',
                'variant' => 'Bordeaux',
                'description' => 'Wine Influence. Rich dishes paired with world-famous Bordeaux wines.',
            ],
            [
                'name' => 'French',
                'variant' => 'Alps',
                'description' => 'Savoyard Cuisine. Focuses on cheese-based, hearty dishes.',
            ],
            [
                'name' => 'Thai',
                'variant' => null,
                'description' => 'A balance of sweet, sour, salty, and spicy flavors, with a focus on fresh herbs like lemongrass and cilantro.',
            ],
            [
                'name' => 'Thai',
                'variant' => 'Central',
                'description' => 'Known for its balance of flavors, rich curries, and fragrant rice dishes.',
            ],
            [
                'name' => 'Thai',
                'variant' => 'Northern',
                'description' => 'Mildly spicy dishes with sticky rice as a staple, influenced by Burmese cuisine.',
            ],
            [
                'name' => 'Thai',
                'variant' => 'Northeastern',
                'description' => 'Isaan Cuisine. Bold, spicy, and sour flavors, with a focus on grilled meats and fermented ingredients.',
            ],
            [
                'name' => 'Thai',
                'variant' => 'Southern',
                'description' => 'Spicy and rich, featuring coconut milk, turmeric, and seafood. Influenced by Malaysian and Indian cuisines.',
            ],
            [
                'name' => 'Spanish',
                'variant' => null,
                'description' => 'Social dining culture with small plates (tapas) and bold, rich flavors.',
            ],
            [
                'name' => 'Spanish',
                'variant' => 'Andalusian',
                'description' => 'Southern Spain. Strongly influenced by Moorish cuisine, featuring olive oil, spices, and fresh produce. Known for light, refreshing dishes to combat the heat.',
            ],
            [
                'name' => 'Spanish',
                'variant' => 'Catalan',
                'description' => 'Rich and flavorful, often combining sweet and savory ingredients. Influenced by French and Mediterranean cuisines.',
            ],
            [
                'name' => 'Spanish',
                'variant' => 'Basque',
                'description' => 'Northern Spain. Known for its high-quality seafood and pintxos (small snacks). Basque chefs are pioneers of modern Spanish gastronomy.',
            ],
            [
                'name' => 'Spanish',
                'variant' => 'Galician',
                'description' => 'Famous for seafood, simple preparations, and hearty meals.',
            ],
            [
                'name' => 'Spanish',
                'variant' => 'Valencian',
                'description' => 'Known as the birthplace of paella and for dishes featuring rice and seafood.',
            ],
            [
                'name' => 'Spanish',
                'variant' => 'Castilian',
                'description' => 'Central Spain. Hearty and rustic, with a focus on roasted meats and stews.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => null,
                'description' => 'Healthy, fresh, and flavorful; uses olive oil, fresh vegetables, legumes, and lean proteins.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => 'Turkish',
                'description' => 'Use of lamb, eggplants, nuts, and yogurt.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => 'Levantine',
                'description' => 'Lebanon, Syria, Jordan, Israel. Fresh vegetables, tahini, chickpeas, and olive oil.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => 'Moroccan',
                'description' => 'Use of spices like cumin, cinnamon, and saffron. Tagines and preserved lemons are common.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => 'Algerian and Tunisian',
                'description' => 'Spicy harissa, olives, and seafood.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => 'Egyptian',
                'description' => 'Hearty stews, legumes, and bread.',
            ],
            [
                'name' => 'Mediterranean',
                'variant' => 'Cypriot',
                'description' => 'Blend of Greek and Turkish influences, with Mediterranean flavors.',
            ],
            [
                'name' => 'Korean',
                'variant' => null,
                'description' => 'Unique flavors (fermented, spicy), interactive dining experiences (BBQ), and growing popularity of Korean culture (K-pop, dramas).',
            ],
            [
                'name' => 'Korean',
                'variant' => 'Jeonju',
                'description' => 'Known for its luxurious Bibimbap and traditional Hanjeongsik (multi-course meal).',
            ],
            [
                'name' => 'Korean',
                'variant' => 'Gyeonggi',
                'description' => 'Features diverse dishes influenced by the capital, Seoul. Known for rice cakes like Tteok.',
            ],
            [
                'name' => 'Korean',
                'variant' => 'Jeju',
                'description' => 'Seafood-focused, with specialties like Jeonbokjuk (abalone porridge) and Black Pork BBQ.',
            ],
            [
                'name' => 'Korean',
                'variant' => 'Gangwon',
                'description' => 'Mountainous area, known for buckwheat-based dishes like Makguksu (cold noodles).',
            ],
            [
                'name' => 'Greek',
                'variant' => null,
                'description' => 'Fresh, wholesome ingredients like olive oil, yogurt, and herbs; associated with Mediterranean health benefits.',
            ],
            [
                'name' => 'Greek',
                'variant' => 'Crete',
                'description' => 'Heavy use of olive oil, wild greens, and raki (a local spirit).',
            ],
            [
                'name' => 'Greek',
                'variant' => 'Cyclades',
                'description' => 'Santorini, Mykonos, etc. Seafood, sun-dried tomatoes, and capers.',
            ],
            [
                'name' => 'Greek',
                'variant' => 'Epirus',
                'description' => 'Mountain cuisine with an emphasis on dairy and meats.',
            ],
            [
                'name' => 'Greek',
                'variant' => 'Thessaloniki and Macedonia',
                'description' => 'Hearty dishes with Eastern Mediterranean influences.',
            ],
            [
                'name' => 'Greek',
                'variant' => 'Ionian Islands',
                'description' => 'Corfu, Kefalonia, etc. Influenced by Venetian cuisine, featuring pasta and seafood.',
            ],
            [
                'name' => 'Middle Eastern',
                'variant' => null,
                'description' => 'Aromatic spices, comforting dishes, and widespread accessibility.',
            ],
            [
                'name' => 'Middle Eastern',
                'variant' => 'Gulf',
                'description' => 'Arabian Peninsula. Includes Saudi Arabia, Yemen, Oman, Qatar, Bahrain, Kuwait, and the UAE. Spiced rice dishes and roasted meats. Heavy use of cardamom, saffron, and dried lime (loomi).',
            ],
            [
                'name' => 'Middle Eastern',
                'variant' => 'Iranian',
                'description' => 'Persian. Known for its use of fragrant herbs, saffron, and dried fruits. Rice as a staple, often prepared with tahdig (crispy crust). Complex stews (khoresh) with a balance of sweet and sour flavors.',
            ],
            [
                'name' => 'Middle Eastern',
                'variant' => 'Iraqi',
                'description' => 'Rich in spices and hearty dishes, influenced by Mesopotamian traditions. Use of tamarind, pomegranate, and dried fruits in stews. Bread as a staple (samoon, flatbread).',
            ],
            [
                'name' => 'Middle Eastern',
                'variant' => 'Yemeni',
                'description' => 'Known for bold, earthy flavors and slow-cooked dishes. Bread as a centerpiece (like malawah or lahoh). Heavy use of fenugreek and cumin.',
            ],
            [
                'name' => 'Middle Eastern',
                'variant' => 'Palestinian',
                'description' => 'Shares similarities with Levantine dishes but with unique touches. Emphasis on olive oil, grains, and legumes. Unique bread and meat preparations.',
            ],
            [
                'name' => 'Vietnamese',
                'variant' => null,
                'description' => 'Light, fresh flavors with herbs, rice noodles, and a balance of sweet and sour.',
            ],
            [
                'name' => 'Vietnamese',
                'variant' => 'Northern',
                'description' => 'Hanoi and Surroundings. This region focuses on subtle flavors, minimal use of spices, and lighter, simpler preparations.',
            ],
            [
                'name' => 'Vietnamese',
                'variant' => 'Central',
                'description' => 'Hue and Da Nang. Known for bold, spicy, and complex flavors, central Vietnamese cuisine is often more challenging for those who are not used to spicy food.',
            ],
            [
                'name' => 'Vietnamese',
                'variant' => 'Southern',
                'description' => 'Ho Chi Minh City, Mekong Delta. Southern Vietnamese cuisine is known for its sweetness, abundance of herbs, and use of fresh produce from the Mekong Delta region.',
            ],
            [
                'name' => 'Lebanese',
                'variant' => null,
                'description' => 'Healthy, plant-based options; rich in flavor and vibrant presentation.',
            ],
            [
                'name' => 'Vegan',
                'variant' => null,
                'description' => 'Popular for its health benefits, environmental sustainability, ethical considerations, and innovative, plant-based alternatives that cater to growing dietary trends and preferences.',
            ],
            [
                'name' => 'Vegetarian',
                'variant' => null,
                'description' => 'Is popular because it offers a healthy, ethical, and environmentally friendly alternative with diverse, flavorful, and adaptable dishes that cater to a wide range of dietary preferences.',
            ],
            [
                'name' => 'Kosher',
                'variant' => null,
                'description' => 'Popular for its cultural and religious significance, high standards of preparation, and appeal to both Jewish communities and others seeking trusted, quality-certified food options.',
            ],
            [
                'name' => 'Halal',
                'variant' => null,
                'description' => 'Is popular for its religious significance, assurance of ethical and hygienic food preparation, and its wide appeal among Muslim communities and others seeking high-quality, trusted food options.',
            ],
            [
                'name' => 'Polish',
                'variant' => null,
                'description' => 'Known for its hearty, comforting dishes featuring rich flavors, traditional recipes, and a blend of Eastern European influences, making it beloved by those seeking satisfying, flavorful meals.',
            ],

        ]);
    }
}
