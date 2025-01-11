<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cuisine;


use App\Entities\Cuisine;
use App\Http\Controllers\Controller;
use App\Repositories\CuisineRepository;
use App\Transformers\CuisineTransformer;
use Illuminate\Http\Request;


class Catalog extends Controller
{

    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly CuisineTransformer  $transformer
    ) {
    }

    /**
     * @param Request $request
     * @return array<Cuisine>
     */
    public function __invoke(Request $request): array
    {

        $cuisines = $this->repository->getCuisines();

        return $this->transformer->transformSet($cuisines);
    }
}