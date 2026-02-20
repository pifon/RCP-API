<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Dishtype;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\DishTypeRepository;
use App\Transformers\v1\DishTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly DishTypeRepository $repository,
        private readonly DishTypeTransformer $transformer,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $dishType = $this->repository->findOneBy(['name' => $slug]);

        if ($dishType === null) {
            throw new NotFoundException("Dish type '{$slug}' not found");
        }

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::single($this->transformer, $dishType, $params),
        );
    }
}
