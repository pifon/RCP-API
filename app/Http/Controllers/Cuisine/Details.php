<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cuisine;

use App\Http\Controllers\Controller;
use App\Repositories\CuisineRepository;
use App\Transformers\CuisineTransformer;
use Illuminate\Http\Request;

class Details extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly CuisineTransformer $transformer
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function __invoke(Request $request, string $slug): array
    {
        $cuisine = $this->repository->getCuisine($slug);

        return $this->transformer->transformDetailed($cuisine);
    }
}
