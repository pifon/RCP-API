<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly RecipeRepository $repository,
        private readonly RecipeTransformer $transformer,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->repository->getRecipe($slug);
        } catch (NoResultException | NonUniqueResultException) {
            throw new NotFoundException("Recipe '{$slug}' not found");
        }

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::single($this->transformer, $recipe, $params),
        );
    }
}
