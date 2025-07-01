<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements Authenticatable
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'username', type: 'string', length: 255, nullable: false)]
    private string $username;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'password', type: 'string', nullable: true)]
    private ?string $password;

    #[ORM\Column(name: 'password_changed_at', type: 'datetime', nullable: false)]
    private ?DateTime $passwordChangedAt;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTimeImmutable $createdAt;

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

    public function getAuthPassword(): ?string
    {
        return $this->password;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): string
    {
        // 'Not implemented.'
        return '';
    }

    /**
     * @param  string  $value
     */
    public function setRememberToken($value): void
    {
        // throw new Exception('Not implemented.');

    }

    /**
     * @throws Exception
     */
    public function getRememberTokenName()
    {
        throw new Exception('Not implemented.');
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

    private function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    private function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    private function setEmail(string $email): void
    {
        $this->email = $email;
    }

    private function setPassword(string $plainPassword): void
    {
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        if (! $hashedPassword) {
            throw new \RuntimeException('Password hashing failed.');
        }

        $this->password = $hashedPassword;
        $this->setPasswordChangedAt(new DateTime);
    }

    public function isPasswordValid(string $plainPassword): bool
    {
        if ($this->password === null) {
            return false;
        }

        return password_verify($plainPassword, $this->password);
    }

    protected function passwordChangedAt(): ?DateTime
    {
        return $this->passwordChangedAt;
    }

    protected function setPasswordChangedAt(?DateTime $passwordChangedAt): void
    {
        $this->passwordChangedAt = $passwordChangedAt;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
