<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\v1\RecipeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'recipes')]
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Column(name: 'id', type: 'integer', unique: true, nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, unique: true, nullable: false)]
    private string $slug;

    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    // Relations

    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    private ?Recipe $variant;

    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private Author $author;

    #[ORM\ManyToOne(targetEntity: Cuisine::class)]
    #[ORM\JoinColumn(name: 'cuisine_id', referencedColumnName: 'id', nullable: false)]
    private Cuisine $cuisine;

    #[ORM\ManyToOne(targetEntity: DishType::class)]
    #[ORM\JoinColumn(name: 'dish_type_id', referencedColumnName: 'id', nullable: false)]
    private DishType $type;

    // Dates

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    private function setVariant(Recipe $variant): void
    {
        $this->variant = $variant;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    private function setCuisine(Cuisine $cuisine): void
    {
        $this->cuisine = $cuisine;
    }

    private function setType(DishType $type): void
    {
        $this->type = $type;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTime;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTime;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function getCuisine(): Cuisine
    {
        return $this->cuisine;
    }

    public function getDishType(): DishType
    {
        return $this->type;
    }

    public function getVariant(): Recipe
    {
        return $this->variant;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
