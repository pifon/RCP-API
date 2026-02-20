<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Pantry;

use App\Entities\PantryItem;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\PantryItemTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly PantryItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $params = QueryParameters::fromArray($request->query->all());

        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(PantryItem::class, 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.expiresAt', 'ASC')
            ->addOrderBy('p.updatedAt', 'DESC');

        if ($params->hasFilter('expired')) {
            if ($params->getFilter('expired') === 'true') {
                $qb->andWhere('p.expiresAt IS NOT NULL')
                    ->andWhere('p.expiresAt < :today')
                    ->setParameter('today', new \DateTime('today'));
            } else {
                $qb->andWhere('(p.expiresAt IS NULL OR p.expiresAt >= :today)')
                    ->setParameter('today', new \DateTime('today'));
            }
        }

        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(PantryItem::class, 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user);

        if ($params->hasFilter('expired')) {
            if ($params->getFilter('expired') === 'true') {
                $countQb->andWhere('p.expiresAt IS NOT NULL')
                    ->andWhere('p.expiresAt < :today')
                    ->setParameter('today', new \DateTime('today'));
            } else {
                $countQb->andWhere('(p.expiresAt IS NULL OR p.expiresAt >= :today)')
                    ->setParameter('today', new \DateTime('today'));
            }
        }

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        $items = $qb->getQuery()->getResult();

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json(
            Document::collection($this->transformer, $items, $params, $pagination),
        );
    }
}
