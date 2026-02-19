<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly RecipeRepository $repository,
        private readonly RecipeTransformer $transformer,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $params = QueryParameters::fromArray($request->query->all());

        $total = $this->repository->countRecipes($params);
        $recipes = $this->repository->listRecipes($params);

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json(
            Document::collection($this->transformer, $recipes, $params, $pagination),
        );
    }
}
