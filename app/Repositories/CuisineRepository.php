<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Cuisine;
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
    public function getCuisines(): array
    {
        $limit = 100;
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->setMaxResults($limit)
            ->addOrderBy('c.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCuisine($slug): Cuisine
    {
        list($name,$variant) = explode('-',$slug);
        if (empty($variant)) {
            $variant = '%';
        }
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.name = :name AND c.variant = :variant')
            ->setParameter('name', ucfirst($name))
            ->setParameter('variant', ucfirst($variant));

        return $qb->getQuery()->getSingleResult();
    }

}