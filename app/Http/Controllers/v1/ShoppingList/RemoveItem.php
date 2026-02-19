<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\ShoppingList;
use App\Entities\ShoppingListItem;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;

class RemoveItem extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(int $listId, int $itemId): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $list = $this->em->getRepository(ShoppingList::class)->findOneBy([
            'id' => $listId,
            'user' => $user,
        ]);

        if ($list === null) {
            throw new NotFoundException('Shopping list not found.');
        }

        $item = $this->em->getRepository(ShoppingListItem::class)->findOneBy([
            'id' => $itemId,
            'shoppingList' => $list,
        ]);

        if ($item === null) {
            throw new NotFoundException('Shopping list item not found.');
        }

        $this->em->remove($item);
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Item removed from shopping list.']),
        );
    }
}
