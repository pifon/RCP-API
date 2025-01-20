<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cuisine;

use App\Exceptions\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Repositories\CuisineRepository;
use App\Repositories\RecipeRepository;
use App\Transformers\RecipeTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Recipes extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeTransformer $recipeTransformer
    ) {}

    /**
     * @return array<string, mixed>
     * @throws ValidationErrorException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $cuisine = $this->repository->getCuisine($slug);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new ValidationErrorException(trans('cuisine.not_found.message'));
        }

        $recipes = $this->recipeRepository->getCuisineRecipes($cuisine, $request->get('limit', 25));

        return $this->recipeTransformer->transformSet($recipes);
    }
}
