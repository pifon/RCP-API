<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'follows')]
#[ORM\Entity]
class Follow
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'follower_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $follower;

    #[ORM\Column(name: 'followable_type', type: 'string', length: 50, nullable: false)]
    private string $followableType;

    #[ORM\Column(name: 'followable_id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    private int $followableId;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFollower(): User
    {
        return $this->follower;
    }

    public function setFollower(User $user): void
    {
        $this->follower = $user;
    }

    public function getFollowableType(): string
    {
        return $this->followableType;
    }

    public function setFollowableType(string $type): void
    {
        $this->followableType = $type;
    }

    public function getFollowableId(): int
    {
        return $this->followableId;
    }

    public function setFollowableId(int $id): void
    {
        $this->followableId = $id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
