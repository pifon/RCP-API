<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Cuisine;
use App\Entities\Recipe;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Recipe::class);
    }

    /**
     * @return Recipe[]
     */
    public function getRecipes(?string $slug, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r');

        if ($slug !== null) {
            $qb->where('r.slug = :slug')
                ->setParameter('slug', $slug);
        }

        $qb->setMaxResults($limit ?? 25);

        /** @var Recipe[] */
        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRecipe(string $slug): Recipe
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.slug = :slug')
            ->setParameter('slug', $slug);

        $qb->setMaxResults(1);

        /** @var Recipe $found */
        $found = $qb->getQuery()->getSingleResult();
        if (! $found) {
            throw new NoResultException;
        }

        return $found;
    }

    public function slugExists(string $slug): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select(1)
            ->where('r.slug = :slug')
            ->setParameter('slug', $slug);

        $count = (int) $qb->getQuery()->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * @return Recipe[]
     */
    public function getCuisineRecipes(Cuisine $cuisine, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('r')  // Alias for the Author entity
            ->select('DISTINCT r')  // Select distinct Author entities
            ->where('r.cuisine_id = :cuisine')  // Filter by the given Cuisine
            ->setParameter('cuisine', $cuisine)  // Set the parameter for the cuisine
            ->setMaxResults($limit ?? 25);

        return $qb->getQuery()->getResult();  // Execute the query and return the results
    }
}
