<?php

declare(strict_types=1);

namespace App\Transformers\v1;

use App\Entities\RecipeComment;
use App\JsonApi\AbstractTransformer;

class CommentTransformer extends AbstractTransformer
{
    public function getType(): string
    {
        return 'comments';
    }

    public function getId(object $entity): string
    {
        /** @var RecipeComment $entity */
        return (string) $entity->getId();
    }

    public function selfLink(object $entity): string
    {
        /** @var RecipeComment $entity */
        $recipeSlug = $entity->getRecipe()->getSlug();

        return "/api/v1/recipes/{$recipeSlug}/comments/{$entity->getId()}";
    }

    protected function attributes(object $entity): array
    {
        /** @var RecipeComment $entity */
        return [
            'body' => $entity->isDeleted() ? null : $entity->getBody(),
            'is-deleted' => $entity->isDeleted(),
            'created-at' => $entity->getCreatedAt()->format('c'),
            'updated-at' => $entity->getUpdatedAt()->format('c'),
        ];
    }

    protected function relationships(object $entity): array
    {
        /** @var RecipeComment $entity */
        $rels = [];

        $user = $entity->getUser();
        $rels['author'] = [
            'data' => ['type' => 'users', 'id' => $user->getUsername()],
        ];

        $parent = $entity->getParent();
        if ($parent !== null) {
            $rels['parent'] = [
                'data' => ['type' => 'comments', 'id' => (string) $parent->getId()],
            ];
        }

        return $rels;
    }
}
