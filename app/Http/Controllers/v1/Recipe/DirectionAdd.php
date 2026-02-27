<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Services\Recipe\DirectionCreationService;
use App\Transformers\v1\DirectionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DirectionAdd extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly DirectionCreationService $directionCreation,
        private readonly DirectionTransformer $transformer,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found");
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];
        $rels = $data['relationships'] ?? [];

        $validator = Validator::make($attrs, [
            'action' => ['required', 'string'],
            'duration-minutes' => ['sometimes', 'integer', 'min:0'],
            'step' => ['sometimes', 'integer', 'min:1'],
            'product-id' => ['sometimes', 'integer'],
            'product-slug' => ['sometimes', 'string'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'measure-id' => ['sometimes', 'integer'],
            'measure-slug' => ['sometimes', 'string'],
            'ingredients' => ['sometimes', 'array'],
            'ingredients.*.product-id' => ['sometimes', 'integer'],
            'ingredients.*.product-slug' => ['sometimes', 'string'],
            'ingredients.*.measure-id' => ['sometimes', 'integer'],
            'ingredients.*.measure-slug' => ['sometimes', 'string'],
            'ingredients.*.amount' => ['sometimes', 'numeric', 'min:0'],
            'ingredients.*.optional' => ['sometimes', 'boolean'],
            'notes' => ['sometimes', 'array'],
            'notes.*' => ['string'],
            'original-text' => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $direction = $this->directionCreation->createDirection($recipe, $attrs, $rels);

        $doc = Document::single($this->transformer, $direction);

        $ingredient = $direction->getIngredient();
        if ($ingredient !== null) {
            $doc['meta'] = [
                'ingredient-linked' => [
                    'id' => $ingredient->getId(),
                    'position' => $ingredient->getPosition(),
                    'product' => $ingredient->getServing()->getProduct()->getName(),
                    'amount' => $ingredient->getServing()->getAmount(),
                    'measure' => $ingredient->getServing()->getMeasure()->getSymbol(),
                ],
                'prep-time-minutes' => $recipe->getPrepTimeMinutes(),
            ];
        } else {
            $doc['meta'] = [
                'prep-time-minutes' => $recipe->getPrepTimeMinutes(),
            ];
        }

        return response()->json($doc, 201);
    }
}
