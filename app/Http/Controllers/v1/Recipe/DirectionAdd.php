<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
use App\Entities\DirectionNote;
use App\Entities\Ingredient;
use App\Entities\IngredientNote;
use App\Entities\Measure;
use App\Entities\Operation;
use App\Entities\Procedure;
use App\Entities\Product;
use App\Entities\Serving;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\DirectionTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DirectionAdd extends Controller
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

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];
        $rels = $data['relationships'] ?? [];

        $validator = Validator::make($attrs, [
            'action' => ['required', 'string'],
            'duration-minutes' => ['sometimes', 'integer', 'min:0'],
            'step' => ['sometimes', 'integer', 'min:1'],
            'product-id' => ['sometimes', 'integer'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'measure-id' => ['sometimes', 'integer'],
            'notes' => ['sometimes', 'array'],
            'notes.*' => ['string'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $conn = $this->em->getConnection();
        $recipeId = $recipe->getId();

        $actionName = $attrs['action'];
        $operation = $this->em->getRepository(Operation::class)->findOneBy(['name' => $actionName]);
        if ($operation === null) {
            $operation = new Operation();
            $operation->setName($actionName);
            $operation->setDescription($actionName);
            $this->em->persist($operation);
        }

        $serving = null;
        $ingredient = null;
        $productId = $attrs['product-id'] ?? ($rels['product']['data']['id'] ?? null);

        if ($productId !== null) {
            $product = $this->em->find(Product::class, (int) $productId);
            if ($product === null) {
                throw new NotFoundException("Product '{$productId}' not found.");
            }

            $measureId = $attrs['measure-id'] ?? ($rels['measure']['data']['id'] ?? null);
            $measure = $measureId !== null ? $this->em->find(Measure::class, (int) $measureId) : null;
            if ($measure === null) {
                throw new ValidationErrorException('Measure required when product is specified.', [
                    'measure-id' => ['Provide measure-id or relationships.measure.'],
                ]);
            }

            $amount = (float) ($attrs['amount'] ?? 0);

            $serving = new Serving();
            $serving->setProduct($product);
            $serving->setAmount($amount);
            $serving->setMeasure($measure);
            $this->em->persist($serving);

            $ingredient = $this->resolveOrCreateIngredient($recipe, $product, $measure, $amount);
        }

        if ($ingredient === null) {
            $explicitIngredientId = $rels['ingredient']['data']['id'] ?? null;
            if ($explicitIngredientId !== null) {
                $ingredient = $this->em->find(Ingredient::class, (int) $explicitIngredientId);
            }
        }

        $procedure = new Procedure();
        $procedure->setOperation($operation);
        $procedure->setServing($serving);
        $procedure->setDuration(isset($attrs['duration-minutes']) ? (int) $attrs['duration-minutes'] : null);
        $this->em->persist($procedure);

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
        $direction->setIngredient($ingredient);
        $direction->setSequence($targetStep);
        $this->em->persist($direction);

        if (!empty($attrs['notes'])) {
            foreach ($attrs['notes'] as $text) {
                $note = new DirectionNote();
                $note->setDirection($direction);
                $note->setNote($text);
                $this->em->persist($note);
            }
        }

        $this->em->flush();

        $this->recalcPrepTime($recipe);

        $this->em->refresh($direction);

        $doc = Document::single($this->transformer, $direction);

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

    /**
     * Find an existing ingredient for the same product in this recipe,
     * and accumulate the amount. If none exists, create a new one.
     */
    private function resolveOrCreateIngredient(
        \App\Entities\Recipe $recipe,
        Product $product,
        Measure $measure,
        float $amount,
    ): Ingredient {
        $conn = $this->em->getConnection();

        $existing = $conn->fetchAssociative(
            'SELECT i.id, s.id AS serving_id, s.amount
             FROM ingredients i
             JOIN servings s ON s.id = i.serving_id
             WHERE i.recipe_id = ? AND s.product_id = ? AND s.measure_id = ?
             LIMIT 1',
            [$recipe->getId(), $product->getId(), $measure->getId()],
        );

        if ($existing !== false) {
            $newAmount = (float) $existing['amount'] + $amount;
            $conn->executeStatement(
                'UPDATE servings SET amount = ?, updated_at = NOW() WHERE id = ?',
                [$newAmount, $existing['serving_id']],
            );

            $ingredient = $this->em->find(Ingredient::class, (int) $existing['id']);
            $this->em->refresh($ingredient);
            $this->em->refresh($ingredient->getServing());

            return $ingredient;
        }

        $maxPos = (int) $conn->fetchOne(
            'SELECT COALESCE(MAX(position), 0) FROM ingredients WHERE recipe_id = ?',
            [$recipe->getId()],
        );

        $ingredientServing = new Serving();
        $ingredientServing->setProduct($product);
        $ingredientServing->setAmount($amount);
        $ingredientServing->setMeasure($measure);
        $this->em->persist($ingredientServing);

        $ingredient = new Ingredient();
        $ingredient->setRecipe($recipe);
        $ingredient->setServing($ingredientServing);
        $ingredient->setPosition($maxPos + 1);
        $this->em->persist($ingredient);

        return $ingredient;
    }

    /**
     * Sum all direction durations and update recipe prep_time_minutes.
     */
    private function recalcPrepTime(\App\Entities\Recipe $recipe): void
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
