<?php

namespace App\Transformers\v1;

use RuntimeException;

/**
 * @template TransformedClass
 */
abstract class TransformerAbstract
{
    /**
     * Transforms a set of items.
     *
     * @param  TransformedClass[]  $set  An array of items to transform.
     * @return array<string, mixed> The transformed items.
     */
    public function transformSet(array $set): array
    {
        return array_map([$this, 'transformItem'], $set);
    }

    /**
     * Transforms a single item.
     *
     * @param  TransformedClass  $item  The item to transform.
     * @return array<string, mixed> The transformed item.
     */
    public function transformItem(mixed $item): array
    {
        if (! method_exists($this, 'transform')) {
            throw new RuntimeException('Missing transform method');
        }

        return $this->transform($item);
    }

    /**
     * Transforms an item (to be implemented in a subclass).
     *
     * @param  TransformedClass  $item  The item to transform.
     * @return array<string, mixed> The transformed representation.
     */
    abstract protected function transform(mixed $item): array;

    /**
     * Transform cuisine object to JSON format for inclusion
     */
    public function transformToJson(mixed $object): array
    {
        $objectName = strtolower(class_basename(get_class($object)));

        return [
            'type' => $objectName,
            'id' => $object->getIdentifier(),
            'attributes' => [
                'name' => $object->getName(),
            ],
            'links' => [
                'self' => route($objectName.'s.show', ['slug' => $object->getIdentifier()]),
            ],
        ];
    }

    public function transformRelationToJson(mixed $subject, string $relation, mixed $related): array
    {
        $relatedName = strtolower(class_basename(get_class($related)));
        $subjectName = strtolower(class_basename(get_class($subject)));
        $subjectId = $subject->getIdentifier();
        $relatedId = $related->getIdentifier();

        return [
            'links' => [
                'self' => "/$subjectName/$subjectId/relationships/$relation",
                'related' => "/$subjectName/$subjectId/$relation",
            ],
            'data' => [
                'type' => "$relatedName",
                'id' => "$relatedId",
            ],
        ];
    }
}
