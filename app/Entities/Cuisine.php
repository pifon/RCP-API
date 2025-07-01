<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\CuisineRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'cuisines')]
#[ORM\Entity(repositoryClass: CuisineRepository::class)]
class Cuisine
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'variant', type: 'string', length: 255, nullable: true)]
    private ?string $variant;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\ManyToMany(targetEntity: Recipe::class)]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection;
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    private function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFullName(): string
    {
        if (! $this->variant) {
            return $this->name;
        }

        return $this->name.' - '.$this->variant;
    }

    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    protected function setVariant(?string $variant): void
    {
        $this->variant = $variant;
    }

    public function getSlug(): string
    {
        if (! $this->variant) {
            return strtolower($this->name);
        }

        return strtolower($this->name.'-'.$this->variant);
    }

    protected function setDescription(?string $description): void
    {
        $this->description = $description;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    private function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    protected function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }
}
