<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Product;

use App\Entities\Product;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\ProductTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Search extends Controller
{
    public function __construct(
        private readonly ProductTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $params = QueryParameters::fromArray($request->query->all());
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(
                Document::collection($this->transformer, [], $params),
            );
        }

        $repo = $this->em->getRepository(Product::class);
        $like = '%' . $query . '%';

        $countQb = $repo->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.name LIKE :q OR p.slug LIKE :q')
            ->setParameter('q', $like);
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $offset = ($params->pageNumber - 1) * $params->pageSize;

        $qb = $repo->createQueryBuilder('p')
            ->where('p.name LIKE :q OR p.slug LIKE :q')
            ->setParameter('q', $like)
            ->orderBy('p.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($params->pageSize);

        $products = $qb->getQuery()->getResult();

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        $doc = Document::collection(
            $this->transformer,
            $products,
            $params,
            $pagination,
        );
        $doc['meta']['search'] = [
            'query' => $query,
            'total-results' => $total,
        ];

        return response()->json($doc);
    }
}
