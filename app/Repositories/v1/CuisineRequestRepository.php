<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\CuisineRequest;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<CuisineRequest>
 */
class CuisineRequestRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, CuisineRequest::class);
    }

    /**
     * @return CuisineRequest[]
     */
    public function listPending(int $page = 1, int $size = 25): array
    {
        return $this->createQueryBuilder('cr')
            ->where('cr.status = :status')
            ->setParameter('status', CuisineRequest::STATUS_PENDING)
            ->orderBy('cr.createdAt', 'ASC')
            ->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size)
            ->getQuery()
            ->getResult();
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('cr')
            ->select('COUNT(cr.id)')
            ->where('cr.status = :status')
            ->setParameter('status', CuisineRequest::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
