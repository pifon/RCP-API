<?php

namespace App\Documentation;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Recipe API",
 *     description="API Recipes"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class RecipeShowDocumentation
{
    /**
     * @OA\Get(
     *     path="/api/v1/recipes/{slug}",
     *     operationId="get_recipe",
     *     summary="Show details of recipe identified by unique slug",
     *     tags={"Recipes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Unique slug identifying recipe",
     *         required=true,
     *         @OA\Schema(type="string", example="focaccia-barese")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recipe details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="recipe"),
     *                 @OA\Property(property="id", type="string", example="focaccia-barese"),
     *                 @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Focaccia Barese"),
     *                     @OA\Property(property="description", type="string", example="In Puglia (the heel of Italy’s boot), focaccia is usually called focaccia barese — named after the city of Bari.\n\nHere are the main details and variations:\n\n🥖 Key traits\nDough: Often enriched with mashed potatoes in addition to flour, making it softer and fluffier.\n\nToppings: Cherry tomatoes (pomodorini), black or green olives, oregano, coarse sea salt, and lots of extra virgin olive oil.\n\nTexture: Crispy edges, soft center.\n\nBaking: Traditionally baked in heavy round pans, often in wood-fired ovens.\n\n📜 Other names / local references\nU' fugass or u' fugassë — in local Barese dialect.\n\nFocaccia pugliese — a general term for the region.\n\nFocaccia con patate — when potatoes are also used as a topping, not just in the dough.\n\n🍅 Fun note\nIn Bari, it’s common to grab a slice of focaccia barese instead of a sandwich — it’s a street snack, breakfast, or lunch on the go. And in small bakeries, the focaccia is often sold by weight, not per slice."),
     *                     @OA\Property(property="cuisine", type="string", example="Italian"),
     *                     @OA\Property(property="author", type="string", example="RCP System Author"),
     *                     @OA\Property(property="dishType", type="string", example="bread"),
     *                     @OA\Property(property="variant", type="string", example="Focaccia")
     *                 ),
     *                 @OA\Property(
     *                     property="relationships",
     *                     type="object",
     *                     @OA\Property(
     *                         property="cuisine",
     *                         type="object",
     *                         @OA\Property(
     *                             property="links",
     *                             type="object",
     *                             @OA\Property(property="self", type="string", example="/recipe/focaccia-barese/relationships/cuisine"),
     *                             @OA\Property(property="related", type="string", example="/recipe/focaccia-barese/cuisine")
     *                         ),
     *                         @OA\Property(
     *                             property="data",
     *                             type="object",
     *                             @OA\Property(property="type", type="string", example="cuisine"),
     *                             @OA\Property(property="id", type="string", example="italian-apulian")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="author",
     *                         type="object",
     *                         @OA\Property(
     *                             property="links",
     *                             type="object",
     *                             @OA\Property(property="self", type="string", example="/recipe/focaccia-barese/relationships/author"),
     *                             @OA\Property(property="related", type="string", example="/recipe/focaccia-barese/author")
     *                         ),
     *                         @OA\Property(
     *                             property="data",
     *                             type="object",
     *                             @OA\Property(property="type", type="string", example="author"),
     *                             @OA\Property(property="id", type="string", example="system")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="dishType",
     *                         type="object",
     *                         @OA\Property(
     *                             property="links",
     *                             type="object",
     *                             @OA\Property(property="self", type="string", example="/recipe/focaccia-barese/relationships/dishType"),
     *                             @OA\Property(property="related", type="string", example="/recipe/focaccia-barese/dishType")
     *                         ),
     *                         @OA\Property(
     *                             property="data",
     *                             type="object",
     *                             @OA\Property(property="type", type="string", example="dishtype"),
     *                             @OA\Property(property="id", type="string", example="bread")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="variant",
     *                         type="object",
     *                         @OA\Property(
     *                             property="links",
     *                             type="object",
     *                             @OA\Property(property="self", type="string", example="/recipe/focaccia-barese/relationships/variant"),
     *                             @OA\Property(property="related", type="string", example="/recipe/focaccia-barese/variant")
     *                         ),
     *                         @OA\Property(
     *                             property="data",
     *                             type="object",
     *                             @OA\Property(property="type", type="string", example="recipe"),
     *                             @OA\Property(property="id", type="string", example="focaccia")
     *                         )
     *                     )
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized — missing or invalid JWT token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="array",
     *                 @OA\Items(type="string", example="unauthenticated")
     *             )
     *         )
     *     )
     * )
     */
    public function getRecipeBySlug()
    {
        // This method exists only for Swagger annotations
    }
}

