<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\IngredientRepository;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\IngredientListTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Ingredients extends Controller
{
    public function __construct(
        private readonly IngredientRepository $repository,
        private readonly RecipeRepository $recipeRepository,
        private readonly IngredientListTransformer $transformer,
    ) {}

    public function __invoke(Request $request, string $slug): array
    {

        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new NotFoundException($e->getMessage());
        }
        $ingredients = $this->repository->findByRecipeId($recipe->getId());

        return $this->transformer->transform($ingredients);
    }
}
