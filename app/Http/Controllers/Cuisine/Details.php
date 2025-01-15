<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cuisine;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\CuisineRepository;
use App\Transformers\CuisineTransformer;
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
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $cuisine = $this->repository->getCuisine($slug);
        } catch (NoResultException $exception) {
            throw new NotFoundException(
                trans('cuisine.details.not_found.message'),
                array(
                    trans('cuisine.details.not_found.error'),
                )
            );
        }

        return $this->transformer->transformDetailed($cuisine);
    }
}
