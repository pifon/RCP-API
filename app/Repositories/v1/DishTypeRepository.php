<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\DishType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<DishType>
 */
class DishTypeRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, DishType::class);
    }

    /**
     * @return DishType[]
     */
    public function getDishTypes(?string $type, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('dt')
            ->select('dt')
            ->setMaxResults($limit ?? 25);

        if ($type) {
            $qb->where('dt.type = :type')
                ->setParameter('type', $type);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getDishType($type): DishType
    {
        $qb = $this->createQueryBuilder('dt')
            ->select('dt')
            ->where('dt.type = :type')
            ->setParameter('type', $type);

        $qb->setMaxResults(1);

        $found = $qb->getQuery()->getSingleResult();
        if (! $found) {
            throw new NoResultException;
        }

        return $found;
    }
}
