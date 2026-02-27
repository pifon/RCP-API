<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'directions')]
#[ORM\Entity]
class Direction
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id', nullable: false)]
    private Recipe $recipe;

    #[ORM\ManyToOne(targetEntity: Procedure::class)]
    #[ORM\JoinColumn(name: 'procedure_id', referencedColumnName: 'id', nullable: false)]
    private Procedure $procedure;

    #[ORM\OneToMany(
        mappedBy: 'direction',
        targetEntity: DirectionIngredient::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $directionIngredients;

    #[ORM\Column(name: 'sequence', type: 'integer', nullable: false)]
    private int $sequence;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\OneToMany(
        mappedBy: 'direction',
        targetEntity: DirectionNote::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->directionIngredients = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
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

    public function getProcedure(): Procedure
    {
        return $this->procedure;
    }

    public function setProcedure(Procedure $procedure): void
    {
        $this->procedure = $procedure;
    }

    /**
     * First linked ingredient, for backward compatibility.
     */
    public function getIngredient(): ?Ingredient
    {
        $first = $this->directionIngredients->first();

        return $first !== false ? $first->getIngredient() : null;
    }

    /**
     * All ingredients linked to this direction (step).
     *
     * @return Collection<int, DirectionIngredient>
     */
    public function getDirectionIngredients(): Collection
    {
        return $this->directionIngredients;
    }

    /**
     * All ingredients linked to this direction (step).
     *
     * @return Ingredient[]
     */
    public function getIngredients(): array
    {
        $out = [];
        foreach ($this->directionIngredients as $di) {
            $out[] = $di->getIngredient();
        }

        return $out;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function setSequence(int $seq): void
    {
        $this->sequence = $seq;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }
}
