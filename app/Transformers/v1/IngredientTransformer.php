<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Ingredient;
use App\JsonApi\AbstractTransformer;

class IngredientTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'ingredients';
    }

    public function getId(object $entity): string
    {
        /** @var Ingredient $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var Ingredient $entity */
        $slug = $entity->getRecipe()->getSlug();

        return "/api/v1/recipes/{$slug}/ingredients/{$entity->getId()}";
    }

    protected function attributes(object $entity): array
    {
        /** @var Ingredient $entity */
        $serving = $entity->getServing();
        $product = $serving->getProduct();
        $measure = $serving->getMeasure();

        $notes = [];
        foreach ($entity->getNotes() as $note) {
            $notes[] = $note->getNote();
        }

        return [
            'position' => $entity->getPosition(),
            'product-name' => $product->getName(),
            'product-slug' => $product->getSlug(),
            'amount' => $serving->getAmount(),
            'measure' => $measure->getName(),
            'measure-symbol' => $measure->getSymbol(),
            'summary' => $this->buildSummary($serving->getAmount(), $measure->getSymbol(), $product->getName()),
            'notes' => $notes ?: null,
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var Ingredient $entity */
        $serving = $entity->getServing();

        return [
            'product' => [
                'data' => ['type' => 'products', 'id' => $serving->getProduct()->getSlug()],
            ],
            'measure' => [
                'data' => ['type' => 'measures', 'id' => (string) $serving->getMeasure()->getId()],
            ],
        ];
    }

    private function buildSummary(float $amount, string $unit, string $name): string
    {
        $qty = ($amount == (int) $amount) ? (int) $amount : $amount;

        return "{$qty}{$unit} {$name}";
    }
}
