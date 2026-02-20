<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\DishType;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Recipe\Concerns\ResolvesCuisine;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use DateTime;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Update extends Controller
{
    use ResolvesCuisine;

    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $attrs = $request->input('data.attributes', []);

        $requestValidator = Validator::make($attrs, [
            'title' => ['sometimes', 'string', 'min:1'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:draft,published'],
            'prep-time-minutes' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'cook-time-minutes' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'difficulty' => ['sometimes', 'nullable', 'in:easy,medium,hard,expert'],
            'serves' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'source-url' => ['sometimes', 'nullable', 'string', 'url', 'max:255'],
            'source-description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],
        ]);

        if ($requestValidator->fails()) {
            throw ValidationErrorException::fromValidationBag($requestValidator->errors());
        }

        $this->applyAttributes($attrs, $recipe);

        $relError = $this->applyRelationships($request->input('data.relationships', []), $recipe);
        if ($relError !== null) {
            return $relError;
        }

        $recipe->setUpdatedAt(new DateTime());

        if (
            array_key_exists('status', $attrs)
            && $attrs['status'] === 'published'
            && $recipe->getPublishedAt() === null
        ) {
            $recipe->setPublishedAt(new DateTime());
        }

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $recipe),
        );
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function applyAttributes(array $attrs, \App\Entities\Recipe $recipe): void
    {
        $setters = [
            'title' => 'setTitle',
            'description' => 'setDescription',
            'status' => 'setStatus',
            'difficulty' => 'setDifficulty',
            'source-url' => 'setSourceUrl',
            'source-description' => 'setSourceDescription',
            'price' => 'setPrice',
            'currency' => 'setCurrency',
        ];

        foreach ($setters as $key => $method) {
            if (array_key_exists($key, $attrs)) {
                $recipe->{$method}($attrs[$key]);
            }
        }

        $intSetters = [
            'prep-time-minutes' => 'setPrepTimeMinutes',
            'cook-time-minutes' => 'setCookTimeMinutes',
            'serves' => 'setServes',
        ];

        foreach ($intSetters as $key => $method) {
            if (array_key_exists($key, $attrs)) {
                $recipe->{$method}($attrs[$key] !== null ? (int) $attrs[$key] : null);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $relationships
     */
    private function applyRelationships(array $relationships, \App\Entities\Recipe $recipe): ?JsonResponse
    {
        if (
            array_key_exists('cuisine', $relationships)
            || array_key_exists('cuisine-request', $relationships)
        ) {
            $cuisineError = $this->applyCuisine($relationships, $recipe, $this->em);
            if ($cuisineError !== null) {
                return $cuisineError;
            }
        }

        if (array_key_exists('dish-type', $relationships)) {
            /** @var array{data: array{id: int|string}|null}|null $dtRel */
            $dtRel = $relationships['dish-type'];
            $dtData = $dtRel['data'] ?? null;
            if (! is_array($dtData)) {
                $recipe->setDishType(null);
            } else {
                $dishType = $this->em->find(DishType::class, (int) $dtData['id']);
                if ($dishType === null) {
                    throw new NotFoundException('Dish type not found.');
                }
                $recipe->setDishType($dishType);
            }
        }

        return null;
    }
}
