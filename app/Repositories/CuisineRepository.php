<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\DishType;
use App\Entities\Cuisine;
use App\Entities\Recipe;
use App\Http\Controllers\Cuisine\Authors;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<Cuisine>
 */
class CuisineRepository extends ServiceEntityRepository
{

    /**
     * @param EntityManager $em
     */
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCuisine($slug): Cuisine
    {
        $name = ucfirst($slug);
        $variant = false;
        if (preg_match('/^[a-z]+-[a-z]+$/', $slug)) {
            list($name, $variant) = explode('-', $slug);
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
        if (!$found) {
            throw new NoResultException();
        }

        return $found;
    }

    /**
     * @param Cuisine $cuisine
     * @param int|null $limit
     * @return Authors[]
     */
    public function getCuisineAuthors(Cuisine $cuisine, ?int $limit = null): array
    {

        // fetch authors from recipes in this cuisine
        $qb = $this->createQueryBuilder('r')
            ->select('DISTINCT author')
            ->innerJoin('r.author', 'author') // Join with the Author entity
            ->where('r.cuisine = :cuisine') // Match the cuisine
            ->setParameter('cuisine', $cuisine);

        return $qb->getQuery()->getResult();
    }

}