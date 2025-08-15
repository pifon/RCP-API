<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Serving;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<Serving>
 */
class ServingRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Serving::class);
    }

    /**
     * @return Serving[]
     */
    public function getById(?int $id, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->setMaxResults($limit ?? 25);

        if ($id) {
            $qb->where('s.id = :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }
}
