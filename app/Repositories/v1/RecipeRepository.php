<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Recipe;
use App\JsonApi\QueryParameters;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    private const ALLOWED_FILTERS = ['status', 'difficulty', 'cuisine', 'author'];
    private const ALLOWED_SORTS = ['title', 'created_at', 'prep_time_minutes', 'cook_time_minutes', 'difficulty'];

    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Recipe::class);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRecipe(string $slug): Recipe
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.slug = :slug')
            ->andWhere('r.deletedAt IS NULL')
            ->setParameter('slug', $slug)
            ->setMaxResults(1);

        /** @var Recipe */
        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return Recipe[]
     */
    public function listRecipes(QueryParameters $params): array
    {
        $qb = $this->createFilteredQuery($params);

        $this->applySorting($qb, $params);

        $qb->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        return $qb->getQuery()->getResult();
    }

    public function countRecipes(QueryParameters $params): int
    {
        $qb = $this->createFilteredQuery($params);
        $qb->select('COUNT(r.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function slugExists(string $slug): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.slug = :slug')
            ->setParameter('slug', $slug);

        return ((int) $qb->getQuery()->getSingleScalarResult()) > 0;
    }

    private function createFilteredQuery(QueryParameters $params): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.deletedAt IS NULL');

        if ($params->hasFilter('status')) {
            $qb->andWhere('r.status = :status')
                ->setParameter('status', $params->getFilter('status'));
        }

        if ($params->hasFilter('difficulty')) {
            $qb->andWhere('r.difficulty = :difficulty')
                ->setParameter('difficulty', $params->getFilter('difficulty'));
        }

        return $qb;
    }

    private function applySorting(QueryBuilder $qb, QueryParameters $params): void
    {
        if ($params->sort === []) {
            $qb->orderBy('r.createdAt', 'DESC');
            return;
        }

        $fieldMap = [
            'title' => 'r.title',
            'created_at' => 'r.createdAt',
            'created-at' => 'r.createdAt',
            'prep_time_minutes' => 'r.prepTimeMinutes',
            'prep-time-minutes' => 'r.prepTimeMinutes',
            'cook_time_minutes' => 'r.cookTimeMinutes',
            'cook-time-minutes' => 'r.cookTimeMinutes',
            'difficulty' => 'r.difficulty',
        ];

        foreach ($params->sort as $sortField) {
            $column = $fieldMap[$sortField->field] ?? null;
            if ($column !== null) {
                $qb->addOrderBy($column, strtoupper($sortField->direction));
            }
        }
    }
}
