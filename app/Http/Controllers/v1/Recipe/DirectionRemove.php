<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
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
    ) {}

    public function __invoke(Request $request, string $slug, int $directionId): JsonResponse
    {
        $direction = $this->em->find(Direction::class, $directionId);

        if ($direction === null || $direction->getRecipe()->getSlug() !== $slug) {
            throw new NotFoundException("Direction not found for recipe '{$slug}'.");
        }

        $recipe = $direction->getRecipe();
        $recipeId = $recipe->getId();
        $removedSeq = $direction->getSequence();

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
}
