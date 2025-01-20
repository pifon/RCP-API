<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\Author;
use App\Entities\Cuisine;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Author::class);
    }

    /**
     * @params string|null $username
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
    public function getAuthor(string $username): Author
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

    /**
     * @param Cuisine $cuisine
     * @param int|null $limit
     * @return Author[]
     */
    public function getCuisineAuthors(Cuisine $cuisine, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('a')  // Alias for the Author entity
        ->select('DISTINCT a')  // Select distinct Author entities
        ->innerJoin('a.recipes', 'r')  // Join the related recipes (Assuming 'recipes' is the property in the Author entity)
        ->where('r.cuisine = :cuisine')  // Filter by the given Cuisine
        ->setParameter('cuisine', $cuisine)  // Set the parameter for the cuisine
        ->setMaxResults($limit ?? 25);

        return $qb->getQuery()->getResult();  // Execute the query and return the results
    }
}