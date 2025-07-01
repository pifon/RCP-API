<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Cuisine;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\CuisineRepository;
use App\Transformers\v1\CuisineTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Details extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly CuisineTransformer $transformer
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $cuisine = $this->repository->getCuisine($slug);
        } catch (NoResultException $exception) {
            throw new NotFoundException(
                trans('cuisine.details.not_found.message'),
                [
                    trans('cuisine.details.not_found.error'),
                ]
            );
        }

        return $this->transformer->transformDetailed($cuisine);
    }
}
