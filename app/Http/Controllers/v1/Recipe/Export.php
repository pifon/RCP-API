<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
use App\Entities\Ingredient;
use App\Entities\Recipe;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\RecipeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Export extends Controller
{
    private const FORMAT_VERSION = '1.0';

    public function __construct(
        private readonly RecipeRepository $recipeRepository,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $export = [
            'pifon-recipe' => self::FORMAT_VERSION,
            'exported-at' => (new \DateTime())->format('c'),
            'recipe' => $this->buildRecipeSection($recipe),
            'ingredients' => $this->buildIngredientsSection($recipe),
            'directions' => $this->buildDirectionsSection($recipe),
        ];

        return response()->json($export, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$slug}.recipe.json\"",
        ]);
    }

    private function buildRecipeSection(Recipe $recipe): array
    {
        $data = [
            'title' => $recipe->getTitle(),
            'slug' => $recipe->getSlug(),
            'description' => $recipe->getDescription(),
            'status' => $recipe->getStatus(),
            'difficulty' => $recipe->getDifficulty(),
            'prep-time-minutes' => $recipe->getPrepTimeMinutes(),
            'cook-time-minutes' => $recipe->getCookTimeMinutes(),
            'serves' => $recipe->getServes(),
            'source-url' => $recipe->getSourceUrl(),
            'source-description' => $recipe->getSourceDescription(),
        ];

        $data['author'] = $recipe->getAuthor()->getName();

        $cuisine = $recipe->getCuisine();
        $data['cuisine'] = $cuisine !== null ? [
            'id' => $cuisine->getId(),
            'name' => $cuisine->getFullName(),
            'slug' => $cuisine->getSlug(),
        ] : null;

        $dishType = $recipe->getDishType();
        $data['dish-type'] = $dishType !== null ? [
            'id' => $dishType->getId(),
            'name' => $dishType->getName(),
        ] : null;

        return $data;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildIngredientsSection(Recipe $recipe): array
    {
        $items = [];

        /** @var Ingredient $ingredient */
        foreach ($recipe->getIngredients() as $ingredient) {
            $serving = $ingredient->getServing();
            $product = $serving->getProduct();
            $measure = $serving->getMeasure();

            $entry = [
                'position' => $ingredient->getPosition(),
                'optional' => $ingredient->isOptional(),
                'product-slug' => $product->getSlug(),
                'product-id' => $product->getId(),
                'product-name' => $product->getName(),
                'amount' => $serving->getAmount(),
                'measure-slug' => $measure->getSlug(),
                'measure-id' => $measure->getId(),
                'measure-name' => $measure->getName(),
                'measure-symbol' => $measure->getSlug(),
            ];

            $notes = [];
            foreach ($ingredient->getNotes() as $n) {
                $notes[] = $n->getNote();
            }
            if ($notes !== []) {
                $entry['notes'] = $notes;
            }

            $items[] = $entry;
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildDirectionsSection(Recipe $recipe): array
    {
        $steps = [];

        /** @var Direction $direction */
        foreach ($recipe->getDirections() as $direction) {
            $procedure = $direction->getProcedure();
            $operation = $procedure->getOperation();

            $entry = [
                'step' => $direction->getSequence(),
                'action' => $operation->getName(),
                'duration-minutes' => $procedure->getDuration(),
            ];

            $procServing = $procedure->getServing();
            if ($procServing !== null) {
                $entry['product-slug'] = $procServing->getProduct()->getSlug();
                $entry['product-id'] = $procServing->getProduct()->getId();
                $entry['product-name'] = $procServing->getProduct()->getName();
                $entry['amount'] = $procServing->getAmount();
                $entry['measure-slug'] = $procServing->getMeasure()->getSlug();
                $entry['measure-id'] = $procServing->getMeasure()->getId();
                $entry['measure-name'] = $procServing->getMeasure()->getName();
            }

            $linkedPositions = [];
            $ingredientsWithAmounts = [];
            foreach ($direction->getDirectionIngredients() as $di) {
                $ing = $di->getIngredient();
                $linkedPositions[] = $ing->getPosition();
                $stepServing = $di->getServing();
                $ingredientsWithAmounts[] = [
                    'position' => $ing->getPosition(),
                    'product-slug' => $stepServing->getProduct()->getSlug(),
                    'product-id' => $stepServing->getProduct()->getId(),
                    'amount' => $stepServing->getAmount(),
                    'measure-slug' => $stepServing->getMeasure()->getSlug(),
                    'measure-id' => $stepServing->getMeasure()->getId(),
                ];
            }
            if ($linkedPositions !== []) {
                $entry['ingredients'] = $linkedPositions;
                $entry['ingredient'] = $linkedPositions[0];
            }
            if (count($ingredientsWithAmounts) > 1) {
                $entry['ingredients-with-amounts'] = $ingredientsWithAmounts;
            }

            $notes = [];
            $originalText = null;
            $isCreator = $this->isRecipeCreator($recipe);
            foreach ($direction->getNotes() as $n) {
                if ($n->isCreatorOnly()) {
                    if ($isCreator) {
                        $originalText = $n->getNote();
                    }
                    continue;
                }
                $notes[] = $n->getNote();
            }
            if ($notes !== []) {
                $entry['notes'] = $notes;
            }
            if ($originalText !== null) {
                $entry['original-text'] = $originalText;
            }

            $steps[] = $entry;
        }

        return $steps;
    }

    private function isRecipeCreator(Recipe $recipe): bool
    {
        $userId = auth()->id();
        if ($userId === null) {
            return false;
        }

        return $recipe->getAuthor()->getUser()->getId() === $userId;
    }
}
