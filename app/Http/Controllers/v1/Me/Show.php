<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Me;

use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly UserTransformer $transformer,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = auth()->user();
        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::single($this->transformer, $user, $params),
        );
    }
}
