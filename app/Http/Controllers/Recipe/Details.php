<?php

declare(strict_types=1);

namespace App\Http\Controllers\Recipe;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\RecipeRepository;
use App\Transformers\RecipeTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Details extends Controller
{
    public function __construct(
        private readonly RecipeRepository $repository,
        private readonly RecipeTransformer $transformer
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $recipe = $this->repository->getRecipe($slug);
        } catch (NoResultException $exception) {
            throw new NotFoundException(
                trans('recipe.details.not_found.message'),
                [
                    trans('recipe.details.not_found.error'),
                ]
            );
        }

        return $this->transformer->transformDetailed($recipe);
    }
}
