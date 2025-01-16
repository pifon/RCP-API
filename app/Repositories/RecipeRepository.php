<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DBAL\ServiceEntityRepository;
use App\Entities\DishType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @extends ServiceEntityRepository<DishType>
 */
class RecipeRepository extends ServiceEntityRepository
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em, DishType::class);
    }

    /**
     * @return DishType[]
     */
    public function getRecipes(?string $slug, ?int $limit): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->setMaxResults($limit ?? 25);

        if ($slug) {
            $qb->where('r.slug = :slug')
                ->setParameter('slug', $slug);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRecipe($slug): DishType
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.name = :slug')
            ->setParameter('slug', $slug);

        $qb->setMaxResults(1);


        $found = $qb->getQuery()->getSingleResult();
        if (!$found) {
            throw new NoResultException();
        }

        return $found;
    }

}