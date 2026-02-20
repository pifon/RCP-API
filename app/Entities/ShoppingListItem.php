<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'shopping_list_items')]
#[ORM\Entity]
class ShoppingListItem
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ShoppingList::class, inversedBy: 'items')]
    #[ORM\JoinColumn(
        name: 'shopping_list_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private ShoppingList $shoppingList;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(
        name: 'quantity',
        type: 'decimal',
        precision: 10,
        scale: 3,
        nullable: false,
        options: ['default' => 1]
    )]
    private string $quantity = '1.000';

    #[ORM\ManyToOne(targetEntity: Measure::class)]
    #[ORM\JoinColumn(name: 'measure_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Measure $measure = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Recipe $recipe = null;

    #[ORM\Column(name: 'checked', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $checked = false;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime;
        $this->updatedAt = new DateTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShoppingList(): ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(ShoppingList $list): void
    {
        $this->shoppingList = $list;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): void
    {
        $this->quantity = $quantity;
        $this->updatedAt = new DateTime;
    }

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function setMeasure(?Measure $measure): void
    {
        $this->measure = $measure;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): void
    {
        $this->checked = $checked;
        $this->updatedAt = new DateTime;
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
