<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'collections')]
#[ORM\Entity]
class Collection
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, nullable: false)]
    private string $slug;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'type', type: 'string', length: 10, nullable: false, options: ['default' => 'bag'])]
    private string $type = 'bag';

    #[ORM\Column(name: 'is_public', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isPublic = false;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?DateTime $deletedAt = null;

    /** @var \Doctrine\Common\Collections\Collection<int, CollectionItem> */
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: CollectionItem::class)]
    private \Doctrine\Common\Collections\Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection;
        $this->createdAt = new DateTime;
        $this->updatedAt = new DateTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function isMenu(): bool
    {
        return $this->type === 'menu';
    }

    /** @return \Doctrine\Common\Collections\Collection<int, CollectionItem> */
    public function getItems(): \Doctrine\Common\Collections\Collection
    {
        return $this->items;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function softDelete(): void
    {
        $this->deletedAt = new DateTime;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTime;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new DateTime;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
        $this->updatedAt = new DateTime;
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    public function getIdentifier(): string
    {
        return (string) $this->id;
    }
}
