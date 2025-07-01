<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\AuthorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'authors')]
#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(name: 'username', type: 'string', length: 255, nullable: false)]
    private string $username;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Recipe::class)]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    private function setId(int $id): void
    {
        $this->id = $id;
    }
    private function setUsername(string $username): void
    {
        $this->username = $username;
    }
    private function setName(string $name): void
    {
        $this->name = $name;
    }
    private function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    private function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    private function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    private function setUser(User $user): void
    {
        $this->user = $user;
    }
}
