<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Pantry;

use App\Entities\PantryItem;
use App\Entities\PantryLog;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;

class Destroy extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $item = $this->em->getRepository(PantryItem::class)->findOneBy([
            'id' => $id,
            'user' => $user,
        ]);

        if ($item === null) {
            throw new NotFoundException('Pantry item not found.');
        }

        $log = new PantryLog();
        $log->setUser($user);
        $log->setProduct($item->getProduct());
        $log->setAction('expired');
        $log->setQuantityChange('-' . $item->getQuantity());
        $log->setNote('Item removed from pantry.');
        $this->em->persist($log);

        $this->em->remove($item);
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Pantry item removed.']),
        );
    }
}
