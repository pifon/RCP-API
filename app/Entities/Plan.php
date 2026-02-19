<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'plans')]
#[ORM\Entity]
class Plan
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, unique: true, nullable: false)]
    private string $slug;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(
        name: 'price_monthly',
        type: 'decimal',
        precision: 8,
        scale: 2,
        nullable: false,
        options: ['default' => '0.00'],
    )]
    private string $priceMonthly = '0.00';

    #[ORM\Column(
        name: 'price_yearly',
        type: 'decimal',
        precision: 8,
        scale: 2,
        nullable: false,
        options: ['default' => '0.00'],
    )]
    private string $priceYearly = '0.00';

    #[ORM\Column(name: 'currency', type: 'string', length: 3, nullable: false, options: ['default' => 'USD'])]
    private string $currency = 'USD';

    #[ORM\Column(name: 'sort_order', type: 'smallint', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $sortOrder = 0;

    #[ORM\Column(name: 'is_active', type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPriceMonthly(): string
    {
        return $this->priceMonthly;
    }

    public function getPriceYearly(): string
    {
        return $this->priceYearly;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isFree(): bool
    {
        return $this->priceMonthly === '0.00' && $this->priceYearly === '0.00';
    }

    public function getIdentifier(): string
    {
        return $this->slug;
    }
}
