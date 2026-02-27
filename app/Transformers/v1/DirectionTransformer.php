<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Direction;
use App\JsonApi\AbstractTransformer;

class DirectionTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'directions';
    }

    public function getId(object $entity): string
    {
        /** @var Direction $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var Direction $entity */
        $slug = $entity->getRecipe()->getSlug();

        return "/api/v1/recipes/{$slug}/directions/{$entity->getId()}";
    }

    protected function attributes(object $entity): array
    {
        /** @var Direction $entity */
        $procedure = $entity->getProcedure();
        $operation = $procedure->getOperation();
        // When step has multiple ingredients, procedure.serving is null; use first direction_ingredient.
        $serving = $procedure->getServing();
        if ($serving === null && $entity->getDirectionIngredients()->count() > 0) {
            $first = $entity->getDirectionIngredients()->first();
            $serving = $first !== false ? $first->getServing() : null;
        }

        $instruction = $this->buildInstruction($entity);

        $isCreator = $this->isRecipeCreator($entity);
        $notes = [];
        foreach ($entity->getNotes() as $note) {
            if ($note->isCreatorOnly() && ! $isCreator) {
                continue;
            }
            $notes[] = $note->getNote();
        }

        return [
            'step' => $entity->getSequence(),
            'action' => $operation->getName(),
            'action-description' => $operation->getDescription(),
            'duration-minutes' => $procedure->getDuration(),
            'instruction' => $instruction,
            'uses-product' => $serving !== null ? $serving->getProduct()->getName() : null,
            'uses-amount' => $serving !== null ? $serving->getAmount() : null,
            'uses-measure' => $serving !== null ? $serving->getMeasure()->getSymbol() : null,
            'notes' => $notes ?: null,
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var Direction $entity */
        $rels = [];
        $ingredients = $entity->getIngredients();
        if ($ingredients !== []) {
            $rels['ingredients'] = [
                'data' => array_map(
                    fn ($ing) => ['type' => 'ingredients', 'id' => (string) $ing->getId()],
                    $ingredients,
                ),
            ];
            // Backward compat: first ingredient as 'ingredient'
            $rels['ingredient'] = [
                'data' => ['type' => 'ingredients', 'id' => (string) $ingredients[0]->getId()],
            ];
        }

        return $rels;
    }

    private function buildInstruction(Direction $entity): string
    {
        $procedure = $entity->getProcedure();
        $operation = $procedure->getOperation();
        $duration = $procedure->getDuration();

        // No ingredients = e.g. "X into Y" transfer step; use first note as instruction if present
        $dirIngs = $entity->getDirectionIngredients();
        if ($dirIngs->count() === 0 && $procedure->getServing() === null) {
            foreach ($entity->getNotes() as $note) {
                if ($note->isCreatorOnly()) {
                    continue;
                }
                $text = trim($note->getNote());
                if ($text !== '') {
                    return str_ends_with($text, '.') ? $text : $text . '.';
                }
            }
        }

        $verb = ucfirst($operation->getName());
        $parts = [$verb];

        // Step amounts: from procedure.serving (single) or from direction_ingredients (multiple)
        if ($dirIngs->count() > 0) {
            $qtyParts = [];
            foreach ($dirIngs as $di) {
                $serving = $di->getServing();
                $product = $serving->getProduct();
                $amount = $serving->getAmount();
                $unit = $serving->getMeasure()->getSymbol();
                $qty = ($amount == (int) $amount) ? (int) $amount : $amount;
                $qtyParts[] = "{$qty}{$unit} {$product->getName()}";
            }
            $parts[] = implode(', ', $qtyParts);
        } elseif ($procedure->getServing() !== null) {
            $serving = $procedure->getServing();
            $product = $serving->getProduct();
            $amount = $serving->getAmount();
            $unit = $serving->getMeasure()->getSymbol();
            $qty = ($amount == (int) $amount) ? (int) $amount : $amount;
            $parts[] = "{$qty}{$unit} {$product->getName()}";
        }

        if ($duration !== null) {
            $parts[] = "for {$duration} minutes";
        }

        return implode(' ', $parts) . '.';
    }

    private function isRecipeCreator(Direction $entity): bool
    {
        $userId = auth()->id();
        if ($userId === null) {
            return false;
        }
        $recipe = $entity->getRecipe();
        $author = $recipe->getAuthor();
        $authorUser = $author->getUser();

        return $authorUser->getId() === $userId;
    }
}
