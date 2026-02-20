<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pantry_items')]
#[ORM\Entity]
class PantryItem
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(name: 'quantity', type: 'decimal', precision: 10, scale: 3, nullable: false)]
    private string $quantity;

    #[ORM\ManyToOne(targetEntity: Measure::class)]
    #[ORM\JoinColumn(name: 'measure_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Measure $measure = null;

    #[ORM\Column(name: 'expires_at', type: 'date', nullable: true)]
    private ?DateTime $expiresAt = null;

    #[ORM\Column(name: 'best_before', type: 'date', nullable: true)]
    private ?DateTime $bestBefore = null;

    #[ORM\Column(name: 'opened_at', type: 'date', nullable: true)]
    private ?DateTime $openedAt = null;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }

    public function getBestBefore(): ?DateTime
    {
        return $this->bestBefore;
    }

    public function getOpenedAt(): ?DateTime
    {
        return $this->openedAt;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt < new DateTime('today');
    }

    public function isPastBestBefore(): bool
    {
        if ($this->bestBefore === null) {
            return false;
        }

        return $this->bestBefore < new DateTime('today');
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function setQuantity(string $quantity): void
    {
        $this->quantity = $quantity;
        $this->updatedAt = new DateTime();
    }

    public function setMeasure(?Measure $measure): void
    {
        $this->measure = $measure;
        $this->updatedAt = new DateTime();
    }

    public function setExpiresAt(?DateTime $date): void
    {
        $this->expiresAt = $date;
        $this->updatedAt = new DateTime();
    }

    public function setBestBefore(?DateTime $date): void
    {
        $this->bestBefore = $date;
        $this->updatedAt = new DateTime();
    }

    public function setOpenedAt(?DateTime $date): void
    {
        $this->openedAt = $date;
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function adjustQuantity(float $delta): void
    {
        $current = (float) $this->quantity;
        $this->quantity = number_format(max(0, $current + $delta), 3, '.', '');
        $this->updatedAt = new DateTime();
    }
}
