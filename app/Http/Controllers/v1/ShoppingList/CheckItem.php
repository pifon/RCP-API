<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\PantryItem;
use App\Entities\PantryLog;
use App\Entities\ShoppingList;
use App\Entities\ShoppingListItem;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\ShoppingListItemTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckItem extends Controller
{
    public function __construct(
        private readonly ShoppingListItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, int $listId, int $itemId): JsonResponse
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

        $data = $request->input('data', []);
        $checked = $data['attributes']['checked'] ?? true;
        $addToPantry = $data['attributes']['add-to-pantry'] ?? false;

        $item->setChecked((bool) $checked);

        if ($checked && $addToPantry) {
            $this->addToPantry($user, $item);
        }

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $item),
        );
    }

    private function addToPantry(\App\Entities\User $user, ShoppingListItem $item): void
    {
        $existing = $this->em->getRepository(PantryItem::class)->findOneBy([
            'user' => $user,
            'product' => $item->getProduct(),
        ]);

        if ($existing !== null) {
            $existing->adjustQuantity((float) $item->getQuantity());
            $pantryItem = $existing;
        } else {
            $pantryItem = new PantryItem;
            $pantryItem->setUser($user);
            $pantryItem->setProduct($item->getProduct());
            $pantryItem->setQuantity($item->getQuantity());
            $pantryItem->setMeasure($item->getMeasure());
            $this->em->persist($pantryItem);
        }

        $log = new PantryLog;
        $log->setUser($user);
        $log->setPantryItem($pantryItem);
        $log->setProduct($item->getProduct());
        $log->setAction('added');
        $log->setQuantityChange($item->getQuantity());
        $log->setSourceType('shopping_list_items');
        $log->setSourceId($item->getId());
        $this->em->persist($log);
    }
}
