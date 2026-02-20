<?php

namespace App\Entities;

use App\Repositories\v1\IngredientRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ingredients')]
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    private Recipe $recipe;

    #[ORM\JoinColumn(name: 'serving_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Serving::class)]
    private Serving $serving;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\OneToMany(
        mappedBy: 'ingredient',
        targetEntity: IngredientNote::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $notes;

    #[ORM\Column(name: 'position', type: 'integer', nullable: false, options: ['default' => 0])]
    private int $position = 0;

    public function __construct()
    {
        $this->notes = new ArrayCollection;
        $this->createdAt = new DateTime;
        $this->updatedAt = new DateTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function getServing(): Serving
    {
        return $this->serving;
    }

    public function setServing(Serving $serving): void
    {
        $this->serving = $serving;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
