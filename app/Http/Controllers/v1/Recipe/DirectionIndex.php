<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\DirectionTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectionIndex extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly DirectionTransformer $transformer,
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

        $directions = $this->em->getRepository(Direction::class)->findBy(
            ['recipe' => $recipe],
            ['sequence' => 'ASC'],
        );

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::collection($this->transformer, $directions, $params),
        );
    }
}
