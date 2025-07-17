<?php

declare(strict_types=1);

namespace App\Entities;

use App\Exceptions\v1\ValidationErrorException;
use App\Repositories\v1\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements Authenticatable, JWTSubject
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'username', type: 'string', length: 255, unique: true, nullable: false)]
    private string $username;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'password', type: 'string', nullable: true)]
    private ?string $password;

    #[ORM\Column(name: 'password_changed_at', type: 'datetime', nullable: false)]
    private ?DateTime $passwordChangedAt;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken(mixed $value): void {}

    public function getRememberTokenName(): string
    {
        return 'token';
    }

    public function getId(): int
    {
        return $this->id;
    }

    private function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPasswordChangedAt(): ?DateTime
    {
        return $this->passwordChangedAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTime('now');
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $plainPassword): void
    {
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        if (! $hashedPassword) {
            throw new ValidationErrorException('Password hashing failed.');
        }

        $this->password = $hashedPassword;
    }

    public function isPasswordValid(string $plainPassword): bool
    {
        if ($this->password === null) {
            return false;
        }

        return password_verify($plainPassword, $this->password);
    }

    public function setPasswordChangedAt(?DateTime $date): void
    {
        $this->passwordChangedAt = $date;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getJWTIdentifier(): int
    {
        return $this->getId();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTime('now');
    }
}
