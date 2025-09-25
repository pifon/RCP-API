<?php

namespace App\Transformers\v1;

class IngredientListTransformer extends TransformerAbstract
{
    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function transform(mixed $item): array
    {
        $items = $this->getRelationships($item);

        return [
            'data' => [
                'type' => 'recipe',
                'id' => $item->getIdentifier(),
                'attributes' => array_merge([
                    'title' => $item->getTitle(),
                    'description' => $item->getDescription(),
                ], $items['attributes']),
                // 'steps' => [], // $item->getSteps()
                'relationships' => $items['relationships'],
            ],
            'links' => [
                'self' => route('recipes.ingredients.show', ['slug' => $item->getSlug()]),
                'recipe' => route('recipes.show', ['slug' => $item->getSlug()]),
                'directions' => route('recipes.directions.show', ['slug' => $item->getSlug()]),
                'describedby' => url('/api/documentation#/Recipes/get_ingredients'),
            ],
            'included' => $items['included'],
        ];
    }

    private function getRelationships(mixed $item): array
    {
        $items = [];
        foreach (['cuisine', 'author', 'dishType', 'variant'] as $key) {
            $method = 'get'.ucfirst($key);
            $object = $item->{$method}();
            if ($object) {
                $items['relationships'][$key] = $this->transformRelationToJson($item, $key, $object);
                $items['included'][] = $this->transformToJson($object);
                $items['attributes'][$key] = $object->getName();
            }

        }

        return $items;
    }
}
