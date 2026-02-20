<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\ShoppingList;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\ShoppingListTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly ShoppingListTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $params = QueryParameters::fromArray($request->query->all());

        $qb = $this->em->createQueryBuilder()
            ->select('sl')
            ->from(ShoppingList::class, 'sl')
            ->where('sl.user = :user')
            ->setParameter('user', $user)
            ->orderBy('sl.updatedAt', 'DESC');

        if ($params->hasFilter('status')) {
            $qb->andWhere('sl.status = :status')
                ->setParameter('status', $params->getFilter('status'));
        }

        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(sl.id)')
            ->from(ShoppingList::class, 'sl')
            ->where('sl.user = :user')
            ->setParameter('user', $user);

        if ($params->hasFilter('status')) {
            $countQb->andWhere('sl.status = :status')
                ->setParameter('status', $params->getFilter('status'));
        }

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        $lists = $qb->getQuery()->getResult();

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json(
            Document::collection($this->transformer, $lists, $params, $pagination),
        );
    }
}
