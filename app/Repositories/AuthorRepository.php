<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Author;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Author::class);
    }

    /**
     * @return Author[]
     */
    public function getAuthors(?string $username, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->setMaxResults($limit ?? 25);

        if ($username) {
            $qb->where('a.username = :username')
                ->setParameter('username', $username);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getAuthor($username): Author
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.name = :username')
            ->setParameter('username', $username);

        $qb->setMaxResults(1);


        $found = $qb->getQuery()->getSingleResult();
        if (!$found) {
            throw new NoResultException();
        }

        return $found;
    }

}