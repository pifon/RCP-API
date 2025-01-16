<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\DishTypeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'dish_types')]
#[ORM\Entity(repositoryClass: DishTypeRepository::class)]
class DishType
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'type', type: 'string', length: 255, nullable: false)]
    private string $type;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

}