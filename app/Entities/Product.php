<?php

namespace App\Entities;

use App\Repositories\v1\ProductRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'products')]
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
    private string $slug;

    #[ORM\ManyToOne(targetEntity: Measure::class)]
    #[ORM\JoinColumn(name: 'measure_id', referencedColumnName: 'id', nullable: true)]
    private ?Measure $measure;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'vegan', type: 'boolean', nullable: false)]
    private bool $vegan;

    #[ORM\Column(name: 'vegetarian', type: 'boolean', nullable: false)]
    private bool $vegetarian;

    #[ORM\Column(name: 'halal', type: 'boolean', nullable: false)]
    private bool $halal;

    #[ORM\Column(name: 'kosher', type: 'boolean', nullable: false)]
    private bool $kosher;

    #[ORM\Column(name: 'allergen', type: 'boolean', nullable: false)]
    private bool $allergen;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function isVegan(): bool
    {
        return $this->vegan;

    }

    public function isVegetarian(): bool
    {
        return $this->vegetarian;
    }

    public function isHalal(): bool
    {
        return $this->halal;
    }

    public function isKosher(): bool
    {
        return $this->kosher;
    }

    public function isAllergen(): bool
    {
        return $this->allergen;
    }
}
