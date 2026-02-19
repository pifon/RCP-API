<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Follow;

use App\Entities\Follow;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $params = QueryParameters::fromArray($request->query->all());

        $qb = $this->em->createQueryBuilder()
            ->select('f')
            ->from(Follow::class, 'f')
            ->where('f.follower = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC');

        if ($params->hasFilter('type')) {
            $qb->andWhere('f.followableType = :type')
                ->setParameter('type', $params->getFilter('type'));
        }

        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(f.id)')
            ->from(Follow::class, 'f')
            ->where('f.follower = :user')
            ->setParameter('user', $user);

        if ($params->hasFilter('type')) {
            $countQb->andWhere('f.followableType = :type')
                ->setParameter('type', $params->getFilter('type'));
        }

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        $follows = $qb->getQuery()->getResult();

        $data = array_map(fn (Follow $f) => [
            'type' => 'follows',
            'id' => (string) $f->getId(),
            'attributes' => [
                'followable-type' => $f->getFollowableType(),
                'followable-id' => $f->getFollowableId(),
                'created-at' => $f->getCreatedAt()->format('c'),
            ],
        ], $follows);

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json([
            'jsonapi' => ['version' => '1.1'],
            'data' => array_values($data),
            'meta' => $pagination->toMeta(),
            'links' => $pagination->toLinks(),
        ]);
    }
}
