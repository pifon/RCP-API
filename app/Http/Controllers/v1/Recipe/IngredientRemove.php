<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Ingredient;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientRemove extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, string $slug, int $ingredientId): JsonResponse
    {
        $ingredient = $this->em->find(Ingredient::class, $ingredientId);

        if ($ingredient === null || $ingredient->getRecipe()->getSlug() !== $slug) {
            throw new NotFoundException("Ingredient not found for recipe '{$slug}'.");
        }

        $this->em->remove($ingredient);
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Ingredient removed.']),
        );
    }
}
