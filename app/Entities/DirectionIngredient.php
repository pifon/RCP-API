<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'direction_ingredients')]
#[ORM\Entity]
class DirectionIngredient
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Direction::class, inversedBy: 'directionIngredients')]
    #[ORM\JoinColumn(name: 'direction_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Direction $direction;

    #[ORM\ManyToOne(targetEntity: Ingredient::class)]
    #[ORM\JoinColumn(name: 'ingredient_id', referencedColumnName: 'id', nullable: false)]
    private Ingredient $ingredient;

    #[ORM\ManyToOne(targetEntity: Serving::class)]
    #[ORM\JoinColumn(name: 'serving_id', referencedColumnName: 'id', nullable: false)]
    private Serving $serving;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function setDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }

    public function getIngredient(): Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(Ingredient $ingredient): void
    {
        $this->ingredient = $ingredient;
    }

    public function getServing(): Serving
    {
        return $this->serving;
    }

    public function setServing(Serving $serving): void
    {
        $this->serving = $serving;
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
