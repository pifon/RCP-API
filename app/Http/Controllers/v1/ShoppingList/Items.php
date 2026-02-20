<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\ShoppingList;
use App\Entities\ShoppingListItem;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\ShoppingListItemTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Items extends Controller
{
    public function __construct(
        private readonly ShoppingListItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, int $listId): JsonResponse
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

        $params = QueryParameters::fromArray($request->query->all());

        $qb = $this->em->createQueryBuilder()
            ->select('i')
            ->from(ShoppingListItem::class, 'i')
            ->where('i.shoppingList = :list')
            ->setParameter('list', $list)
            ->orderBy('i.checked', 'ASC')
            ->addOrderBy('i.createdAt', 'DESC');

        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(i.id)')
            ->from(ShoppingListItem::class, 'i')
            ->where('i.shoppingList = :list')
            ->setParameter('list', $list);

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
