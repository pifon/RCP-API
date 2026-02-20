<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Destroy extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $recipe->softDelete();
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Recipe deleted.']),
        );
    }
}
