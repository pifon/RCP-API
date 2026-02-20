<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\CuisineRequest;

use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\Repositories\v1\CuisineRequestRepository;
use App\Transformers\v1\CuisineRequestTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly CuisineRequestRepository $repository,
        private readonly CuisineRequestTransformer $transformer,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $page = (int) $request->query('page.number', '1');
        $size = min((int) $request->query('page.size', '25'), 100);

        $items = $this->repository->listPending($page, $size);
        $total = $this->repository->countPending();

        $pagination = new Pagination(
            total: $total,
            currentPage: $page,
            perPage: $size,
            baseUrl: '/api/v1/cuisine-requests',
        );

        return response()->json(
            Document::collection($this->transformer, $items, pagination: $pagination),
        );
    }
}
