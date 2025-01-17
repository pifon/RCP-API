<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cuisine;

use App\Exceptions\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorRepository;
use App\Repositories\CuisineRepository;
use App\Transformers\AuthorTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Authors extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly AuthorRepository $authorRepository,
        private readonly AuthorTransformer $AuthorTransformer
    ) {}

    /**
     * @return array<string, mixed>
     * @throws ValidationErrorException
     */
    public function __invoke(Request $request, string $slug): array
    {
        try {
            $cuisine = $this->repository->getCuisine($slug);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new ValidationErrorException(trans('cuisine.not_found.message'));
        }
        $authors = $this->authorRepository->getCuisineAuthors($cuisine);

        return $this->AuthorTransformer->transformSet($authors);
    }
}
