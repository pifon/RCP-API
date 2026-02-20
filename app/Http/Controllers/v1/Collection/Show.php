<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\CollectionRepository;
use App\Transformers\v1\CollectionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly CollectionRepository $repository,
        private readonly CollectionTransformer $transformer,
    ) {}

    public function __invoke(Request $request, int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $collection = $this->repository->findByIdForUser($id, $user);
        if ($collection === null) {
            throw new NotFoundException('Collection not found.');
        }

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::single($this->transformer, $collection, $params),
        );
    }
}
