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
        $serving = $procedure->getServing();

        $instruction = $this->buildInstruction($entity);

        $notes = [];
        foreach ($entity->getNotes() as $note) {
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

        $ingredient = $entity->getIngredient();
        if ($ingredient !== null) {
            $rels['ingredient'] = [
                'data' => ['type' => 'ingredients', 'id' => (string) $ingredient->getId()],
            ];
        }

        return $rels;
    }

    private function buildInstruction(Direction $entity): string
    {
        $procedure = $entity->getProcedure();
        $operation = $procedure->getOperation();
        $serving = $procedure->getServing();
        $duration = $procedure->getDuration();

        $verb = ucfirst($operation->getName());
        $parts = [$verb];

        if ($serving !== null) {
            $product = $serving->getProduct();
            $amount = $serving->getAmount();
            $unit = $serving->getMeasure()->getSymbol();
            $qty = ($amount == (int) $amount) ? (int) $amount : $amount;
            $parts[] = "{$qty}{$unit} {$product->getName()}";
        }

        if ($duration !== null) {
            $parts[] = "for {$duration} minutes";
        }

        return implode(' ', $parts).'.';
    }
}
