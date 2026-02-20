<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\CollectionRepository;
use App\Transformers\v1\CollectionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly CollectionRepository $repository,
        private readonly CollectionTransformer $transformer,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $params = QueryParameters::fromArray($request->query->all());

        $total = $this->repository->countForUser($user, $params);
        $collections = $this->repository->listForUser($user, $params);

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json(
            Document::collection($this->transformer, $collections, $params, $pagination),
        );
    }
}
