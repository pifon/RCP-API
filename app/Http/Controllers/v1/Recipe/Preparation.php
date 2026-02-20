<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\DirectionTransformer;
use App\Transformers\v1\IngredientTransformer;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Preparation extends Controller
{
    public function __construct(
        private readonly RecipeRepository $repository,
        private readonly RecipeTransformer $recipeTransformer,
        private readonly IngredientTransformer $ingredientTransformer,
        private readonly DirectionTransformer $directionTransformer,
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

        $doc = Document::single($this->recipeTransformer, $recipe, $params);

        $ingredients = $recipe->getIngredients()->toArray();
        usort($ingredients, fn ($a, $b) => $a->getPosition() <=> $b->getPosition() ?: $a->getId() <=> $b->getId());

        $ingredientResources = [];
        foreach ($ingredients as $ingredient) {
            $ingredientResources[] = $this->ingredientTransformer->toResource($ingredient, $params);
        }

        $directions = $recipe->getDirections()->toArray();
        usort($directions, fn ($a, $b) => $a->getSequence() <=> $b->getSequence());

        $directionResources = [];
        foreach ($directions as $direction) {
            $directionResources[] = $this->directionTransformer->toResource($direction, $params);
        }

        $doc['data']['relationships']['ingredients'] = [
            'data' => array_map(
                fn ($r) => ['type' => $r['type'], 'id' => $r['id']],
                $ingredientResources,
            ),
            'links' => [
                'related' => "/api/v1/recipes/{$slug}/ingredients",
            ],
            'meta' => ['count' => count($ingredientResources)],
        ];

        $doc['data']['relationships']['directions'] = [
            'data' => array_map(
                fn ($r) => ['type' => $r['type'], 'id' => $r['id']],
                $directionResources,
            ),
            'links' => [
                'related' => "/api/v1/recipes/{$slug}/directions",
            ],
            'meta' => ['count' => count($directionResources)],
        ];

        $included = $doc['included'] ?? [];
        $included = array_merge($included, $ingredientResources, $directionResources);
        $doc['included'] = $included;

        return response()->json($doc);
    }
}
