<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'collection_items')]
#[ORM\Entity]
class CollectionItem
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'collection_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Collection $collection;

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Recipe $recipe;

    #[ORM\Column(name: 'position', type: 'integer', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $position = 0;

    #[ORM\Column(name: 'scheduled_date', type: 'date', nullable: true)]
    private ?DateTime $scheduledDate = null;

    #[ORM\Column(name: 'meal_slot', type: 'string', length: 20, nullable: true)]
    private ?string $mealSlot = null;

    #[ORM\Column(name: 'note', type: 'text', nullable: true)]
    private ?string $note = null;

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

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getScheduledDate(): ?DateTime
    {
        return $this->scheduledDate;
    }

    public function getMealSlot(): ?string
    {
        return $this->mealSlot;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setCollection(Collection $collection): void
    {
        $this->collection = $collection;
    }

    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
        $this->updatedAt = new DateTime();
    }

    public function setScheduledDate(?DateTime $date): void
    {
        $this->scheduledDate = $date;
        $this->updatedAt = new DateTime();
    }

    public function setMealSlot(?string $slot): void
    {
        $this->mealSlot = $slot;
        $this->updatedAt = new DateTime();
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
