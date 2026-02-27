<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\Author;
use App\Entities\User;
use App\JsonApi\AbstractTransformer;
use App\Repositories\v1\AuthorRepository;
use Doctrine\ORM\NoResultException;

class UserTransformer extends AbstractTransformer
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function getType(): string
    {
        return 'users';
    }

    public function getId(object $entity): string
    {
        /** @var User $entity */
        return $entity->getUsername();
    }

    public function selfLink(object $entity): string
    {
        return '/api/v1/me';
    }

    protected function attributes(object $entity): array
    {
        /** @var User $entity */
        $attrs = [
            'username' => $entity->getUsername(),
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'created-at' => $entity->getCreatedAt()?->format('c'),
        ];

        $attrs['author'] = $this->authorAttribute($entity);

        return $attrs;
    }

    /**
     * Author attribute for the current user: null if no author, otherwise object with id, name, username, tier.
     *
     * @return array<string, mixed>|null
     */
    private function authorAttribute(User $user): ?array
    {
        try {
            $author = $this->authorRepository->getAuthor($user);
        } catch (NoResultException) {
            return null;
        }

        return $this->authorToAttribute($author);
    }

    /**
     * @return array<string, mixed>
     */
    private function authorToAttribute(Author $author): array
    {
        return [
            'id' => $author->getIdentifier(),
            'name' => $author->getName(),
            'username' => $author->getUsername(),
            'tier' => $author->getTier(),
            'created-at' => $author->getCreatedAt()->format('c'),
        ];
    }
}
