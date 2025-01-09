<?php

namespace App\Transformers;

use RuntimeException;

/**
 * @template TransformedClass
 */
abstract class TransformerAbstract
{
    /**
     * @param TransformedClass[] $set
     */
    public function transformSet(array $set): array
    {
        return array_map([$this, 'transformItem'], $set);
    }

    public function transformItem($item): array
    {
        if (!method_exists($this, 'transform')) {
            throw new RuntimeException("Missing transform method");
        }

        return $this->transform($item);
    }
}