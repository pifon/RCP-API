<?php

declare(strict_types=1);

namespace App\Http\Controllers\Author;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorRepository;
use App\Transformers\AuthorTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly AuthorRepository $repository,
        private readonly AuthorTransformer $transformer
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws NotFoundException
     */
    public function __invoke(Request $request, string $username): array
    {
        try {
            $author = $this->repository->getAuthor($username);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $this->transformer->transform($author);
    }
}
