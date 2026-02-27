<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
use App\Entities\DirectionIngredient;
use App\Entities\Serving;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectionRemove extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, string $slug, int $directionId): JsonResponse
    {
        $direction = $this->em->find(Direction::class, $directionId);

        if ($direction === null || $direction->getRecipe()->getSlug() !== $slug) {
            throw new NotFoundException("Direction not found for recipe '{$slug}'.");
        }

        $recipe = $direction->getRecipe();
        $recipeId = $recipe->getId();
        $removedSeq = $direction->getSequence();

        // Re-evaluate each direction_ingredient (update or remove recipe ingredients), then remove each di.
        // Removing the direction also cascade-deletes any direction_ingredients (Direction has orphanRemoval).
        $this->reEvaluateIngredientForRemovedDirection($direction);

        $this->em->remove($direction);
        $this->em->flush();

        $conn = $this->em->getConnection();

        $conn->executeStatement(
            'UPDATE directions SET sequence = sequence - 1 WHERE recipe_id = ? AND sequence > ?',
            [$recipeId, $removedSeq],
        );

        $total = (int) $conn->fetchOne(
            'SELECT COALESCE(SUM(p.duration), 0)
             FROM directions d
             JOIN procedures p ON p.id = d.procedure_id
             WHERE d.recipe_id = ?',
            [$recipeId],
        );

        $recipe->setPrepTimeMinutes($total > 0 ? $total : null);
        $this->em->persist($recipe);
        $this->em->flush();

        return response()->json(
            Document::meta([
                'message' => 'Direction removed. Remaining steps renumbered.',
                'prep-time-minutes' => $recipe->getPrepTimeMinutes(),
            ]),
        );
    }

    /**
     * Re-evaluate all ingredients linked to this direction: for each direction_ingredient,
     * subtract this step's amount from the ingredient's serving; remove ingredient if
     * new amount <= 0, otherwise update its serving.
     */
    private function reEvaluateIngredientForRemovedDirection(Direction $direction): void
    {
        $directionIngredients = $direction->getDirectionIngredients()->toArray();
        foreach ($directionIngredients as $di) {
            $this->reEvaluateOneIngredientForRemovedDirection($direction, $di);
        }
    }

    private function reEvaluateOneIngredientForRemovedDirection(Direction $direction, DirectionIngredient $di): void
    {
        $stepServing = $di->getServing();
        $ingredient = $di->getIngredient();
        $currentServing = $ingredient->getServing();
        $product = $currentServing->getProduct();
        $measure = $currentServing->getMeasure();

        if (
            $stepServing->getProduct()->getId() !== $product->getId()
            || $stepServing->getMeasure()->getId() !== $measure->getId()
        ) {
            return;
        }

        $stepAmount = (float) $stepServing->getAmount();
        $currentAmount = (float) $currentServing->getAmount();
        $newAmount = max(0.0, $currentAmount - $stepAmount);

        $direction->getDirectionIngredients()->removeElement($di);
        $this->em->remove($di);
        $this->em->flush();

        if ($newAmount <= 0) {
            $this->em->remove($ingredient);
            return;
        }

        $newServing = $this->em->getRepository(Serving::class)->findOneBy([
            'product' => $product,
            'measure' => $measure,
            'amount' => $newAmount,
        ]);

        if ($newServing === null) {
            $newServing = new Serving();
            $newServing->setProduct($product);
            $newServing->setMeasure($measure);
            $newServing->setAmount($newAmount);
            $this->em->persist($newServing);
        }

        $ingredient->setServing($newServing);
        $this->em->persist($ingredient);
    }
}
