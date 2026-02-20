<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\CollectionRepository;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;

class Destroy extends Controller
{
    public function __construct(
        private readonly CollectionRepository $repository,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $collection = $this->repository->findByIdForUser($id, $user);
        if ($collection === null) {
            throw new NotFoundException('Collection not found.');
        }

        $collection->softDelete();
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Collection deleted.']),
        );
    }
}
