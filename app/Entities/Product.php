<?php

declare(strict_types=1);

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

    #[ORM\Column(name: 'slug', type: 'string', length: 255, unique: true, nullable: false)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: Measure::class)]
    #[ORM\JoinColumn(name: 'measure_id', referencedColumnName: 'id', nullable: true)]
    private ?Measure $measure;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'vegan', type: 'boolean', nullable: false)]
    private bool $vegan = false;

    #[ORM\Column(name: 'vegetarian', type: 'boolean', nullable: false)]
    private bool $vegetarian = false;

    #[ORM\Column(name: 'halal', type: 'boolean', nullable: false)]
    private bool $halal = false;

    #[ORM\Column(name: 'kosher', type: 'boolean', nullable: false)]
    private bool $kosher = false;

    // Taste profile (0-100)

    #[ORM\Column(name: 'taste_sweet', type: 'smallint', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $tasteSweet = 0;

    #[ORM\Column(name: 'taste_sour', type: 'smallint', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $tasteSour = 0;

    #[ORM\Column(name: 'taste_salty', type: 'smallint', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $tasteSalty = 0;

    #[ORM\Column(
        name: 'taste_bitter',
        type: 'smallint',
        nullable: false,
        options: ['unsigned' => true, 'default' => 0],
    )]
    private int $tasteBitter = 0;

    #[ORM\Column(name: 'taste_umami', type: 'smallint', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $tasteUmami = 0;

    // Nutrition per 100g

    #[ORM\Column(name: 'calories_per_100g', type: 'decimal', precision: 7, scale: 2, nullable: true)]
    private ?string $caloriesPer100g = null;

    #[ORM\Column(name: 'protein_per_100g', type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $proteinPer100g = null;

    #[ORM\Column(name: 'carbs_per_100g', type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $carbsPer100g = null;

    #[ORM\Column(name: 'fat_per_100g', type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $fatPer100g = null;

    #[ORM\Column(name: 'fiber_per_100g', type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $fiberPer100g = null;

    // Shelf life

    #[ORM\Column(name: 'shelf_life_days', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $shelfLifeDays = null;

    #[ORM\Column(name: 'shelf_life_opened_days', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $shelfLifeOpenedDays = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
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

    public function getTasteSweet(): int
    {
        return $this->tasteSweet;
    }

    public function getTasteSour(): int
    {
        return $this->tasteSour;
    }

    public function getTasteSalty(): int
    {
        return $this->tasteSalty;
    }

    public function getTasteBitter(): int
    {
        return $this->tasteBitter;
    }

    public function getTasteUmami(): int
    {
        return $this->tasteUmami;
    }

    public function getCaloriesPer100g(): ?string
    {
        return $this->caloriesPer100g;
    }

    public function getProteinPer100g(): ?string
    {
        return $this->proteinPer100g;
    }

    public function getCarbsPer100g(): ?string
    {
        return $this->carbsPer100g;
    }

    public function getFatPer100g(): ?string
    {
        return $this->fatPer100g;
    }

    public function getFiberPer100g(): ?string
    {
        return $this->fiberPer100g;
    }

    public function getShelfLifeDays(): ?int
    {
        return $this->shelfLifeDays;
    }

    public function getShelfLifeOpenedDays(): ?int
    {
        return $this->shelfLifeOpenedDays;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getIdentifier(): string
    {
        return $this->slug;
    }
}
