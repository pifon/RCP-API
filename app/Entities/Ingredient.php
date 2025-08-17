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

    #[ORM\OneToMany(mappedBy: 'ingredient', targetEntity: IngredientNote::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;

    }

    public function getServing(): Serving
    {
        return $this->serving;
    }
}
