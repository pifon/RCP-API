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
    public function getDishTypes(?string $name, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('dt')
            ->select('dt')
            ->setMaxResults($limit ?? 25);

        if ($name) {
            $qb->where('dt.name = :name')
                ->setParameter('name', $name);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getDishType($name): DishType
    {
        $qb = $this->createQueryBuilder('dt')
            ->select('dt')
            ->where('dt.name = :name')
            ->setParameter('name', $name);

        $qb->setMaxResults(1);

        $found = $qb->getQuery()->getSingleResult();
        if (! $found) {
            throw new NoResultException;
        }

        return $found;
    }
}
