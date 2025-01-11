<?php

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Cuisine;
use Doctrine\ORM\EntityManager;

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
     * @return array<Cuisine>
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

}