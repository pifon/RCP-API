<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Cuisine;
use App\JsonApi\QueryParameters;
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

    public function findBySlug(string $slug): ?Cuisine
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * @return Cuisine[]
     */
    public function listAll(QueryParameters $params): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        return $qb->getQuery()->getResult();
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
