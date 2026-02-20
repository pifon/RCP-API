<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Entities\CollectionItem;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\CollectionRepository;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;

class RemoveItem extends Controller
{
    public function __construct(
        private readonly CollectionRepository $collectionRepository,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(int $collectionId, int $itemId): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $collection = $this->collectionRepository->findByIdForUser($collectionId, $user);
        if ($collection === null) {
            throw new NotFoundException('Collection not found.');
        }

        $item = $this->em->getRepository(CollectionItem::class)->findOneBy([
            'id' => $itemId,
            'collection' => $collection,
        ]);

        if ($item === null) {
            throw new NotFoundException('Collection item not found.');
        }

        $this->em->remove($item);
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Item removed from collection.']),
        );
    }
}
