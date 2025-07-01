<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Cuisine;

use App\Http\Controllers\Controller;
use App\Repositories\v1\CuisineRepository;
use App\Transformers\v1\CuisineTransformer;
use Illuminate\Http\Request;

class Show extends Controller
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

        return $this->transformer->transform($cuisine);
    }
}
