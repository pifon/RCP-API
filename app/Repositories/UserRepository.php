<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em, User::class);
    }

    /**
     * @params int $id
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getUser(int $id): User
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        $qb->setMaxResults(1);


        $found = $qb->getQuery()->getSingleResult();
        if (!$found) {
            throw new NoResultException();
        }

        return $found;
    }

}