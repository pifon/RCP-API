<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Ingredient;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\IngredientTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientIndex extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly IngredientTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found");
        }

        $ingredients = $this->em->getRepository(Ingredient::class)->findBy(
            ['recipe' => $recipe],
            ['position' => 'ASC', 'id' => 'ASC'],
        );

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::collection($this->transformer, $ingredients, $params),
        );
    }
}
