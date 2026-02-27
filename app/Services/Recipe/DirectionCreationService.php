<?php

declare(strict_types=1);

namespace App\Services\Recipe;

use App\Entities\Direction;
use App\Entities\DirectionIngredient;
use App\Entities\DirectionNote;
use App\Entities\Ingredient;
use App\Entities\Measure;
use App\Entities\Operation;
use App\Entities\Procedure;
use App\Entities\Product;
use App\Entities\Recipe;
use App\Entities\Serving;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\v1\Recipe\Concerns\ResolvesProductAndMeasure;
use Doctrine\ORM\EntityManager;

class DirectionCreationService
{
    use ResolvesProductAndMeasure;

    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    /**
     * Create one direction for the recipe from structured attributes.
     * Attributes: action (required), duration-minutes, step, notes (array), original-text.
     * Single ingredient: product-id/product-slug, measure-id/measure-slug, amount.
     * Multiple ingredients: ingredients (array of {product-id|product-slug, measure-id|measure-slug, amount}).
     * Relationships: product.data.id, measure.data.id, ingredient.data.id (optional).
     *
     * @param array<string, mixed> $attrs
     * @param array<string, mixed> $rels
     */
    public function createDirection(Recipe $recipe, array $attrs, array $rels = []): Direction
    {
        $actionName = $attrs['action'] ?? '';
        if ($actionName === '') {
            throw new ValidationErrorException('Action is required.', ['action' => ['Required.']]);
        }

        $operation = $this->em->getRepository(Operation::class)->findOneBy(['name' => $actionName]);
        if ($operation === null) {
            $operation = new Operation();
            $operation->setName($actionName);
            $operation->setDescription($actionName);
            $this->em->persist($operation);
        }

        $ingredientSpecs = $this->collectIngredientSpecs($attrs, $rels);
        $firstServing = null;
        $directionIngredients = [];

        foreach ($ingredientSpecs as $spec) {
            $context = [
                'amount' => $spec['amount'],
                'measure-slug' => (string) $spec['measure'],
            ];
            $product = $this->resolveProductByIdOrSlug($this->em, $spec['product'], $context);
            $measure = $this->resolveMeasureByIdOrSlug($this->em, $spec['measure']);
            $amount = (float) $spec['amount'];
            if ($amount <= 0) {
                throw new ValidationErrorException('Amount must be greater than zero.', [
                    'amount' => ['Provide a positive amount.'],
                ]);
            }
            $serving = $this->findOrCreateServing($product, $measure, $amount);
            if ($firstServing === null) {
                $firstServing = $serving;
            }
            $optional = (bool) ($spec['optional'] ?? false);
            $ingredient = $this->linkServingToIngredientsList(
                $recipe,
                $product,
                $measure,
                $amount,
                $serving,
                $optional,
            );
            $directionIngredients[] = ['ingredient' => $ingredient, 'serving' => $serving];
        }

        $duration = isset($attrs['duration-minutes']) ? (int) $attrs['duration-minutes'] : null;
        // When multiple ingredients in one step, don't tie procedure to one serving (operation applies to all).
        $procedureServing = count($ingredientSpecs) > 1 ? null : $firstServing;
        $procedure = $this->findOrCreateProcedure($operation, $procedureServing, $duration);

        $conn = $this->em->getConnection();
        $recipeId = $recipe->getId();
        $maxSeq = (int) $conn->fetchOne(
            'SELECT COALESCE(MAX(sequence), 0) FROM directions WHERE recipe_id = ?',
            [$recipeId],
        );

        $targetStep = isset($attrs['step']) ? (int) $attrs['step'] : ($maxSeq + 1);

        if ($targetStep <= $maxSeq) {
            $conn->executeStatement(
                'UPDATE directions SET sequence = sequence + 1 WHERE recipe_id = ? AND sequence >= ?',
                [$recipeId, $targetStep],
            );
        }

        $direction = new Direction();
        $direction->setRecipe($recipe);
        $direction->setProcedure($procedure);
        $direction->setSequence($targetStep);
        $this->em->persist($direction);

        foreach ($directionIngredients as $item) {
            $di = new DirectionIngredient();
            $di->setDirection($direction);
            $di->setIngredient($item['ingredient']);
            $di->setServing($item['serving']);
            $direction->getDirectionIngredients()->add($di);
            $this->em->persist($di);
        }

        if (! empty($attrs['notes'])) {
            foreach ($attrs['notes'] as $text) {
                $note = new DirectionNote();
                $note->setDirection($direction);
                $note->setNote($text);
                $this->em->persist($note);
            }
        }

        if (! empty($attrs['original-text'])) {
            $creatorNote = new DirectionNote();
            $creatorNote->setDirection($direction);
            $creatorNote->setNote($attrs['original-text']);
            $creatorNote->setCreatorOnly(true);
            $this->em->persist($creatorNote);
        }

        $this->em->flush();

        $this->recalcPrepTime($recipe);

        $this->em->refresh($direction);

        return $direction;
    }

    /**
     * Collect one or more ingredient specs from attrs/rels.
     * Returns list of ['product' => id|slug, 'measure' => id|slug, 'amount' => float].
     *
     * @param array<string, mixed> $attrs
     * @param array<string, mixed> $rels
     * @return list<array{product: mixed, measure: mixed, amount: float}>
     */
    private function collectIngredientSpecs(array $attrs, array $rels): array
    {
        $specs = [];
        if (! empty($attrs['ingredients']) && is_array($attrs['ingredients'])) {
            foreach ($attrs['ingredients'] as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $productRef = $item['product-slug'] ?? $item['product-id'] ?? null;
                $measureRef = $item['measure-slug'] ?? $item['measure-id'] ?? null;
                if ($productRef === null || $measureRef === null) {
                    continue;
                }
                $amount = (float) ($item['amount'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }
                $specs[] = [
                    'product' => $productRef,
                    'measure' => $measureRef,
                    'amount' => $amount,
                    'optional' => (bool) ($item['optional'] ?? false),
                ];
            }
        }
        if ($specs !== []) {
            return $specs;
        }
        $productRef = $attrs['product-slug'] ?? $attrs['product-id'] ?? ($rels['product']['data']['id'] ?? null);
        if ($productRef !== null) {
            $measureRef = $attrs['measure-slug'] ?? $attrs['measure-id'] ?? ($rels['measure']['data']['id'] ?? null);
            if ($measureRef !== null) {
                $amount = (float) ($attrs['amount'] ?? 0);
                if ($amount > 0) {
                    $specs[] = [
                        'product' => $productRef,
                        'measure' => $measureRef,
                        'amount' => $amount,
                        'optional' => (bool) ($attrs['optional'] ?? false),
                    ];
                }
            }
        }
        return $specs;
    }

    private function findOrCreateProcedure(Operation $operation, ?Serving $serving, ?int $duration): Procedure
    {
        $criteria = [
            'operation' => $operation,
            'serving' => $serving,
            'duration' => $duration,
        ];

        $existing = $this->em->getRepository(Procedure::class)->findOneBy($criteria);
        if ($existing !== null) {
            return $existing;
        }

        $procedure = new Procedure();
        $procedure->setOperation($operation);
        $procedure->setServing($serving);
        $procedure->setDuration($duration);
        $this->em->persist($procedure);

        return $procedure;
    }

    private function findOrCreateServing(Product $product, Measure $measure, float $amount): Serving
    {
        $existing = $this->em->getRepository(Serving::class)->findOneBy([
            'product' => $product,
            'measure' => $measure,
            'amount' => $amount,
        ]);

        if ($existing !== null) {
            return $existing;
        }

        $serving = new Serving();
        $serving->setProduct($product);
        $serving->setMeasure($measure);
        $serving->setAmount($amount);
        $this->em->persist($serving);

        return $serving;
    }

    private function linkServingToIngredientsList(
        Recipe $recipe,
        Product $product,
        Measure $measure,
        float $amount,
        Serving $currentServing,
        bool $optional = false,
    ): Ingredient {
        $conn = $this->em->getConnection();

        $existingRow = $conn->fetchAssociative(
            'SELECT i.id, i.position, s.id AS serving_id, s.amount AS serving_amount
             FROM ingredients i
             JOIN servings s ON s.id = i.serving_id
             WHERE i.recipe_id = ? AND s.product_id = ? AND s.measure_id = ?
             LIMIT 1',
            [$recipe->getId(), $product->getId(), $measure->getId()],
        );

        if ($existingRow === false) {
            $maxPos = (int) $conn->fetchOne(
                'SELECT COALESCE(MAX(position), 0) FROM ingredients WHERE recipe_id = ?',
                [$recipe->getId()],
            );
            $ingredient = new Ingredient();
            $ingredient->setRecipe($recipe);
            $ingredient->setServing($currentServing);
            $ingredient->setPosition($maxPos + 1);
            $ingredient->setOptional($optional);
            $this->em->persist($ingredient);

            return $ingredient;
        }

        $existingAmount = (float) $existingRow['serving_amount'];
        $cumulativeAmount = $existingAmount + $amount;
        $superServing = $this->findOrCreateServing($product, $measure, $cumulativeAmount);

        $ingredient = $this->em->find(Ingredient::class, (int) $existingRow['id']);
        $ingredient->setServing($superServing);
        if ($optional) {
            $ingredient->setOptional(true);
        }
        $this->em->persist($ingredient);

        return $ingredient;
    }

    private function recalcPrepTime(Recipe $recipe): void
    {
        $total = $this->em->getConnection()->fetchOne(
            'SELECT COALESCE(SUM(p.duration), 0)
             FROM directions d
             JOIN procedures p ON p.id = d.procedure_id
             WHERE d.recipe_id = ?',
            [$recipe->getId()],
        );

        $minutes = (int) $total;
        $recipe->setPrepTimeMinutes($minutes > 0 ? $minutes : null);
        $this->em->persist($recipe);
        $this->em->flush();
    }
}
