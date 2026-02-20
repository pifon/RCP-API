<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\v1\RecipeRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'status', type: 'string', length: 20, nullable: false, options: ['default' => 'draft'])]
    private string $status = 'draft';

    #[ORM\Column(name: 'prep_time_minutes', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $prepTimeMinutes = null;

    #[ORM\Column(name: 'cook_time_minutes', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $cookTimeMinutes = null;

    #[ORM\Column(name: 'difficulty', type: 'string', length: 10, nullable: true)]
    private ?string $difficulty = null;

    #[ORM\Column(name: 'serves', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $serves = null;

    #[ORM\Column(name: 'published_at', type: 'datetime', nullable: true)]
    private ?DateTime $publishedAt = null;

    #[ORM\Column(name: 'source_url', type: 'string', length: 255, nullable: true)]
    private ?string $sourceUrl = null;

    #[ORM\Column(name: 'source_description', type: 'string', length: 255, nullable: true)]
    private ?string $sourceDescription = null;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(name: 'currency', type: 'string', length: 3, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(
        name: 'fork_revenue_percent',
        type: 'smallint',
        nullable: false,
        options: ['unsigned' => true, 'default' => 0],
    )]
    private int $forkRevenuePercent = 0;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?DateTime $deletedAt = null;

    // Relations

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', nullable: true)]
    private ?Recipe $variant = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'forked_from_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Recipe $forkedFrom = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private Author $author;

    #[ORM\ManyToOne(targetEntity: Cuisine::class)]
    #[ORM\JoinColumn(name: 'cuisine_id', referencedColumnName: 'id', nullable: true)]
    private ?Cuisine $cuisine = null;

    #[ORM\ManyToOne(targetEntity: CuisineRequest::class)]
    #[ORM\JoinColumn(name: 'cuisine_request_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?CuisineRequest $cuisineRequest = null;

    #[ORM\ManyToOne(targetEntity: DishType::class)]
    #[ORM\JoinColumn(name: 'dish_type_id', referencedColumnName: 'id', nullable: true)]
    private ?DishType $dishType = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Ingredient::class)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    private Collection $ingredients;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Direction::class)]
    #[ORM\OrderBy(['sequence' => 'ASC'])]
    private Collection $directions;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getPrepTimeMinutes(): ?int
    {
        return $this->prepTimeMinutes;
    }

    public function setPrepTimeMinutes(?int $minutes): void
    {
        $this->prepTimeMinutes = $minutes;
    }

    public function getCookTimeMinutes(): ?int
    {
        return $this->cookTimeMinutes;
    }

    public function setCookTimeMinutes(?int $minutes): void
    {
        $this->cookTimeMinutes = $minutes;
    }

    public function getTotalTimeMinutes(): ?int
    {
        if ($this->prepTimeMinutes === null && $this->cookTimeMinutes === null) {
            return null;
        }

        return ($this->prepTimeMinutes ?? 0) + ($this->cookTimeMinutes ?? 0);
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(?string $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    public function getServes(): ?int
    {
        return $this->serves;
    }

    public function setServes(?int $serves): void
    {
        $this->serves = $serves;
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(?string $url): void
    {
        $this->sourceUrl = $url;
    }

    public function getSourceDescription(): ?string
    {
        return $this->sourceDescription;
    }

    public function setSourceDescription(?string $desc): void
    {
        $this->sourceDescription = $desc;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): void
    {
        $this->price = $price;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function isFree(): bool
    {
        return $this->price === null;
    }

    public function getForkRevenuePercent(): int
    {
        return $this->forkRevenuePercent;
    }

    public function setForkRevenuePercent(int $percent): void
    {
        $this->forkRevenuePercent = $percent;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    public function getCuisine(): ?Cuisine
    {
        return $this->cuisine;
    }

    public function setCuisine(?Cuisine $cuisine): void
    {
        $this->cuisine = $cuisine;
    }

    public function getCuisineRequest(): ?CuisineRequest
    {
        return $this->cuisineRequest;
    }

    public function setCuisineRequest(?CuisineRequest $cuisineRequest): void
    {
        $this->cuisineRequest = $cuisineRequest;
    }

    public function getDishType(): ?DishType
    {
        return $this->dishType;
    }

    public function setDishType(?DishType $type): void
    {
        $this->dishType = $type;
    }

    public function getVariant(): ?Recipe
    {
        return $this->variant;
    }

    public function setVariant(?Recipe $variant): void
    {
        $this->variant = $variant;
    }

    public function getForkedFrom(): ?Recipe
    {
        return $this->forkedFrom;
    }

    public function setForkedFrom(?Recipe $recipe): void
    {
        $this->forkedFrom = $recipe;
    }

    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function getDirections(): Collection
    {
        return $this->directions;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $dt = null): void
    {
        $this->createdAt = $dt ?? new DateTime();
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $dt = null): void
    {
        $this->updatedAt = $dt ?? new DateTime();
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function softDelete(): void
    {
        $this->deletedAt = new DateTime();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function getIdentifier(): string
    {
        return $this->slug;
    }
}
