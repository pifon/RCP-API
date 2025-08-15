<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Measure;
use App\Entities\Product;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Product::class);
    }

    public function getById(?int $id): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->setMaxResults(1);

        if ($id) {
            $qb->where('p.id = :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleResult();
    }
}
