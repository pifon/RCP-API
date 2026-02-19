<?php

declare(strict_types=1);

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Collection;
use App\Entities\User;
use App\JsonApi\QueryParameters;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<Collection>
 */
class CollectionRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Collection::class);
    }

    /**
     * @return Collection[]
     */
    public function listForUser(User $user, QueryParameters $params): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('user', $user);

        if ($params->hasFilter('type')) {
            $qb->andWhere('c.type = :type')
                ->setParameter('type', $params->getFilter('type'));
        }

        $qb->orderBy('c.updatedAt', 'DESC')
            ->setFirstResult(($params->pageNumber - 1) * $params->pageSize)
            ->setMaxResults($params->pageSize);

        return $qb->getQuery()->getResult();
    }

    public function countForUser(User $user, QueryParameters $params): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('user', $user);

        if ($params->hasFilter('type')) {
            $qb->andWhere('c.type = :type')
                ->setParameter('type', $params->getFilter('type'));
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByIdForUser(int $id, User $user): ?Collection
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andWhere('c.user = :user')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function slugExistsForUser(string $slug, User $user): bool
    {
        $count = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.slug = :slug')
            ->andWhere('c.user = :user')
            ->setParameter('slug', $slug)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $count) > 0;
    }
}
