<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\AuthorRepository;
use App\Repositories\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements Authenticatable
{
    /**
     * @var int
     */
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
     * @param string $value
     */
    public function setRememberToken($value): void
    {
        //throw new Exception('Not implemented.');
        return;
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

    public function getPasswordChangedAt(): ?DateTime
    {
        return $this->passwordChangedAt;
    }

}