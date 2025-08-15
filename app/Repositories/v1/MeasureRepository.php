<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Measure;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<Measure>
 */
class MeasureRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Measure::class);
    }

    public function getById(?int $id): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->setMaxResults(1);

        if ($id) {
            $qb->where('s.id = :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleResult();
    }
}
