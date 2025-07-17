<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Author;

use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\UserRepository;
use App\Transformers\v1\AuthorTransformer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly AuthorRepository $repository,
        private readonly UserRepository $userRepository,
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
            $user = $this->userRepository->getUserByUsername($username);
            if ($user === null) {
                throw new NotFoundException("User '{$username}' not found");
            }
            $author = $this->repository->getAuthor($user);
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $this->transformer->transform($author);
    }
}
