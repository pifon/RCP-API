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
            ->select('r')
            ->setMaxResults($limit ?? 25);

        if ($slug) {
            $qb->where('r.slug = :slug')
                ->setParameter('slug', $slug);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRecipe($slug): Recipe
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.slug = :slug')
            ->setParameter('slug', $slug);

        $qb->setMaxResults(1);

        $found = $qb->getQuery()->getSingleResult();
        if (! $found) {
            throw new NoResultException;
        }

        return $found;
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
