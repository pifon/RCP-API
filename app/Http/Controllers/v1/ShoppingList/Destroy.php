<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\ShoppingList;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;

class Destroy extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {}

    public function __invoke(int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $list = $this->em->getRepository(ShoppingList::class)->findOneBy([
            'id' => $id,
            'user' => $user,
        ]);

        if ($list === null) {
            throw new NotFoundException('Shopping list not found.');
        }

        $this->em->remove($list);
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Shopping list deleted.']),
        );
    }
}
