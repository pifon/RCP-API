<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\v1\CuisineRequestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'cuisine_requests')]
#[ORM\Entity(repositoryClass: CuisineRequestRepository::class)]
class CuisineRequest
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    #[ORM\Column(name: 'id', type: 'bigint', unique: true, nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'variant', type: 'string', length: 255, nullable: true)]
    private ?string $variant = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'status', type: 'string', length: 20, nullable: false, options: ['default' => 'pending'])]
    private string $status = self::STATUS_PENDING;

    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: 'requested_by', referencedColumnName: 'id', nullable: false)]
    private Author $requestedBy;

    #[ORM\ManyToOne(targetEntity: Cuisine::class)]
    #[ORM\JoinColumn(name: 'cuisine_id', referencedColumnName: 'id', nullable: true)]
    private ?Cuisine $cuisine = null;

    #[ORM\Column(name: 'admin_notes', type: 'text', nullable: true)]
    private ?string $adminNotes = null;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(?string $variant): void
    {
        $this->variant = $variant;
    }

    public function getFullName(): string
    {
        if ($this->variant === null) {
            return $this->name;
        }

        return $this->name.' - '.$this->variant;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getRequestedBy(): Author
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(Author $author): void
    {
        $this->requestedBy = $author;
    }

    public function getCuisine(): ?Cuisine
    {
        return $this->cuisine;
    }

    public function setCuisine(?Cuisine $cuisine): void
    {
        $this->cuisine = $cuisine;
    }

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function setAdminNotes(?string $notes): void
    {
        $this->adminNotes = $notes;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $dt = null): void
    {
        $this->updatedAt = $dt ?? new DateTime;
    }
}
