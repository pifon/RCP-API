<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly RecipeRepository $repository,
        private readonly RecipeTransformer $transformer
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws NotFoundException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $recipe = $this->repository->getRecipe($slug);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $this->transformer->transform($recipe);
    }
}
