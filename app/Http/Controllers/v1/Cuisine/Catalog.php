<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Cuisine;

use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\CuisineRepository;
use App\Transformers\v1\CuisineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Catalog extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly CuisineTransformer $transformer,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $params = QueryParameters::fromArray($request->query->all());

        $total = $this->repository->countAll();
        $cuisines = $this->repository->listAll($params);

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json(
            Document::collection($this->transformer, $cuisines, $params, $pagination),
        );
    }
}
