<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Cuisine;

use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\CuisineRepository;
use App\Transformers\v1\AuthorTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Authors extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly AuthorRepository $authorRepository,
        private readonly AuthorTransformer $authorTransformer
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws ValidationErrorException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $cuisine = $this->repository->getCuisine($slug);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new ValidationErrorException(trans('cuisine.not_found.message'));
        }

        $authors = $this->authorRepository->getCuisineAuthors($cuisine, $request->get('limit', 25));

        return $this->authorTransformer->transformSet($authors);
    }
}
