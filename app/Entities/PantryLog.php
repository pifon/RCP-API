<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pantry_logs')]
#[ORM\Entity]
class PantryLog
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: PantryItem::class)]
    #[ORM\JoinColumn(name: 'pantry_item_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?PantryItem $pantryItem = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(name: 'action', type: 'string', length: 20, nullable: false)]
    private string $action;

    #[ORM\Column(name: 'quantity_change', type: 'decimal', precision: 10, scale: 3, nullable: false)]
    private string $quantityChange;

    #[ORM\Column(name: 'source_type', type: 'string', length: 255, nullable: true)]
    private ?string $sourceType = null;

    #[ORM\Column(name: 'source_id', type: 'integer', nullable: true, options: ['unsigned' => true])]
    private ?int $sourceId = null;

    #[ORM\Column(name: 'note', type: 'text', nullable: true)]
    private ?string $note = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getPantryItem(): ?PantryItem
    {
        return $this->pantryItem;
    }

    public function setPantryItem(?PantryItem $item): void
    {
        $this->pantryItem = $item;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getQuantityChange(): string
    {
        return $this->quantityChange;
    }

    public function setQuantityChange(string $change): void
    {
        $this->quantityChange = $change;
    }

    public function setSourceType(?string $type): void
    {
        $this->sourceType = $type;
    }

    public function setSourceId(?int $id): void
    {
        $this->sourceId = $id;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
