<?php

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Repositories\v1\IngredientRepository;
use App\Repositories\v1\RecipeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Ingredients
{
    public function __construct(
        private readonly IngredientRepository $repository,
        private readonly RecipeRepository $recipeRepository,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new NotFoundException($e->getMessage());
        }
        $ingredients = $this->repository->findByRecipeId($recipe->getId());

        // return $this->transformer->transform($ingredients);

        return response()->json($ingredients);
    }
}
