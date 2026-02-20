<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'shopping_lists')]
#[ORM\Entity]
class ShoppingList
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: \App\Entities\Collection::class)]
    #[ORM\JoinColumn(name: 'collection_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?\App\Entities\Collection $collection = null;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'status', type: 'string', length: 20, nullable: false, options: ['default' => 'active'])]
    private string $status = 'active';

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    /** @var Collection<int, ShoppingListItem> */
    #[ORM\OneToMany(mappedBy: 'shoppingList', targetEntity: ShoppingListItem::class)]
    private Collection $items;

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

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCollection(): ?\App\Entities\Collection
    {
        return $this->collection;
    }

    public function setCollection(?\App\Entities\Collection $collection): void
    {
        $this->collection = $collection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTime;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
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

    /** @return Collection<int, ShoppingListItem> */
    public function getItems(): Collection
    {
        return $this->items;
    }
}
