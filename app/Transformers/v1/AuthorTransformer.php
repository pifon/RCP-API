<?php

namespace App\Transformers\v1;

// use App\Entities\Author;
// use App\Transformers\TransformerAbstract;

use DateTimeInterface;

class AuthorTransformer extends TransformerAbstract
{
    /**
     * {@inheritDoc}
     */
    public function transform(mixed $item): array
    {
        return [
            'name' => $item->getName(),
            '_links' => [
                'self' => route('authors.show', ['username' => $item->getUsername()]),
                'details' => route('authors.details', ['username' => $item->getUsername()]),
            ],
        ];
    }

    public function transformDetailed(mixed $item): array
    {
        return [
            'name' => $item->getFullName(),
            'username' => $item->getUsername(),
            'description' => $item->getDescription(),
            'created_at' => $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => $this->getDetailedLinks($item),
        ];
    }

    private function getDetailedLinks(mixed $item): array
    {
        return [
            'self' => route('author.details', ['username' => $item->getUsername()]),
            'handle' => route('author.show', ['username' => $item->getUsername()]),
            // 'recipes' => route('author.recipes', ['username' => $item->getUsername()]),
            // 'cuisines' => route('author.cuisines', ['username' => $item->getUsername()]),
            // 'related' => route('author.related', ['username' => $item->getUsername()]),
        ];
    }
}
