<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\CuisineRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

#[ORM\Table(name: 'cuisines')]
#[ORM\Entity(repositoryClass: CuisineRepository::class)]
class Cuisine
{
    /**
     * @var int
     */
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

    #[ORM\OneToMany(mappedBy: 'cuisine', targetEntity: DishType::class)]
    private Collection $recipes;

    public function getId(): int
    {
        return $this->id;
    }
    public function getFullName(): string
    {
        if(!$this->variant) {
            return $this->name;
        }
        return $this->name . ' - ' . $this->variant;
    }

    public function getSlug(): string
    {
        if(!$this->variant) {
            return strtolower($this->name);
        }
        return strtolower($this->name . '-' . $this->variant);
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

    public function getName()
    {
        return $this->name;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }
}