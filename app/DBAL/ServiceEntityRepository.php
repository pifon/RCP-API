<?php

declare(strict_types=1);

namespace App\DBAL;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * @template T of object
 *
 * @extends EntityRepository<T>
 */
class ServiceEntityRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    /**
     * @param EntityManager $em
     * @param string $entityClass
     */
    public function __construct(EntityManager $em, string $entityClass)
    {
        $classMetadata = $em->getClassMetadata($entityClass);
        parent::__construct($em, $classMetadata);
    }
}