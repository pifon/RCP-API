<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Author;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\AuthorRepository;
use App\Transformers\v1\AuthorTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Details extends Controller
{
    public function __construct(
        private readonly AuthorRepository $repository,
        private readonly AuthorTransformer $transformer
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, string $username): array
    {
        try {
            $author = $this->repository->getAuthor($username);
        } catch (NoResultException $exception) {
            throw new NotFoundException(
                trans('author.details.not_found.message'),
                [
                    trans('author.details.not_found.error'),
                ]
            );
        }

        return $this->transformer->transformDetailed($author);
    }
}
