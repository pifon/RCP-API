<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Cuisine;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\CuisineRepository;
use App\Transformers\v1\CuisineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly CuisineTransformer $transformer,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $cuisine = $this->repository->findBySlug($slug);

        if ($cuisine === null) {
            throw new NotFoundException("Cuisine '{$slug}' not found");
        }

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::single($this->transformer, $cuisine, $params),
        );
    }
}
