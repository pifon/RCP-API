<?php

namespace App\Transformers;

# use App\Entities\Author;
# use App\Transformers\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract
{

    /**
     * @inheritDoc
     */
    public function transform(mixed $item): array
    {
        return [
            'name' => $item->getName(),
            '_links' => [
                'self' => route('authors.show', ['username' => $item->getUsername()]),
                'details' => route('authors.details', ['username' => $item->getUsername()]),
            ]
        ];
    }
}