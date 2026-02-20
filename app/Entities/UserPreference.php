<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_preferences')]
#[ORM\Entity]
class UserPreference
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(
        name: 'spice_tolerance',
        type: 'smallint',
        nullable: false,
        options: ['unsigned' => true, 'default' => 50],
    )]
    private int $spiceTolerance = 50;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getSpiceTolerance(): int
    {
        return $this->spiceTolerance;
    }

    public function setSpiceTolerance(int $value): void
    {
        $this->spiceTolerance = $value;
        $this->updatedAt = new DateTime;
    }
}
