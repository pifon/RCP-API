<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Follow;

use App\Entities\Follow;
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

        $follow = $this->em->getRepository(Follow::class)->findOneBy([
            'id' => $id,
            'follower' => $user,
        ]);

        if ($follow === null) {
            throw new NotFoundException('Follow not found.');
        }

        $this->em->remove($follow);
        $this->em->flush();

        return response()->json(
            Document::meta(['message' => 'Unfollowed.']),
        );
    }
}
