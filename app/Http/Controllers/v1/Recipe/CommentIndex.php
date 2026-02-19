<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\RecipeComment;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\CommentTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentIndex extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly CommentTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (NoResultException) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $params = QueryParameters::fromArray($request->query->all());

        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from(RecipeComment::class, 'c')
            ->where('c.recipe = :recipe')
            ->andWhere('c.parent IS NULL')
            ->setParameter('recipe', $recipe)
            ->orderBy('c.createdAt', 'DESC');

        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(RecipeComment::class, 'c')
            ->where('c.recipe = :recipe')
            ->andWhere('c.parent IS NULL')
            ->setParameter('recipe', $recipe);

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        $comments = $qb->getQuery()->getResult();

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        return response()->json(
            Document::collection($this->transformer, $comments, $params, $pagination),
        );
    }
}
