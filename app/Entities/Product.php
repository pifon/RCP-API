<?php

namespace App\Entities;

use App\Enums\MeasureType;
use App\Repositories\v1\ProductRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'measures')]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, nullable: true)]
    private string $abbr;

    #[ORM\JoinColumn(name: 'measure_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Measure::class)]
    private Measure $measure;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'vegan', type: 'boolean', nullable: false)]
    private bool $isVegan;
    #[ORM\Column(name: 'vegetarian', type: 'boolean', nullable: false)]
    private bool $isVegetarian;

    #[ORM\Column(name: 'halal', type: 'boolean', nullable: false)]
    private bool $isHalal;
    #[ORM\Column(name: 'kosher', type: 'boolean', nullable: false)]
    private bool $isKosher;

    #[ORM\Column(name: 'allergen', type: 'boolean', nullable: false)]
    private bool $isAllergen;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;
}
