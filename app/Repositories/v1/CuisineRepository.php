<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Author;
use App\Entities\Cuisine;
use App\Entities\Recipe;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<Cuisine>
 */
class CuisineRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Cuisine::class);
    }

    /**
     * @return Cuisine[]
     */
    public function getCuisines(?string $name, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->setMaxResults($limit ?? 25);

        if ($name) {
            $qb->where('c.name = :name')
                ->setParameter('name', ucfirst($name));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @params string $slug
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCuisine(string $slug): Cuisine
    {
        $name = ucfirst($slug);
        $variant = false;
        if (preg_match('/^[a-z]+-[a-z]+$/', $slug)) {
            [$name, $variant] = explode('-', $slug);
        }

        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.name = :name')
            ->setParameter('name', ucfirst($name));

        if ($variant) {
            $qb->andWhere('c.variant = :variant')
                ->setParameter('variant', ucfirst($variant));
        }
        $qb->setMaxResults(1);

        $found = $qb->getQuery()->getSingleResult();
        if (! $found) {
            throw new NoResultException;
        }

        return $found;
    }

    /**
     * @return Author[]
     */
    public function getCuisineAuthors(Cuisine $cuisine, ?int $limit = null): array
    {

        // fetch authors from recipes in this cuisine
        $qb = $this->createQueryBuilder('r')
            ->select('DISTINCT author')
            ->innerJoin('r.author_id', 'author') // Join with the Author entity
            ->where('r.cuisine = :cuisine') // Match the cuisine
            ->setParameter('cuisine', $cuisine);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Recipe[]
     */
    public function getCuisineRecipes(Cuisine $cuisine, ?int $limit = null): array
    {
        // fetch authors from recipes in this cuisine
        $qb = $this->createQueryBuilder('r')
            ->select('recipes')
            ->where('r.cuisine = :cuisine') // Match the cuisine
            ->setParameter('cuisine', $cuisine);

        return $qb->getQuery()->getResult();
    }

    public function getCuisineIngredients(string $slug): array
    {
        // Correct to fetch ingredients
        $qb = $this->createQueryBuilder('r')
            ->select('recipes')
            ->where('r.slug = :cuisine') // Match the cuisine
            ->setParameter('cuisine', $slug);

        return $qb->getQuery()->getResult();
    }

    public function getCuisineRelates(string $slug): array
    {
        // fetch related cuisines
        $qb = $this->createQueryBuilder('r')
            ->select('recipes')
            ->where('r.slug = :cuisine') // Match the cuisine
            ->setParameter('cuisine', $slug);

        return $qb->getQuery()->getResult();
    }
}
