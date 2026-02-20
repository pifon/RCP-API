<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'direction_notes')]
#[ORM\Entity]
class DirectionNote
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Direction::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'direction_id', referencedColumnName: 'id', nullable: false)]
    private Direction $direction;

    #[ORM\Column(name: 'note', type: 'text', nullable: false)]
    private string $note;

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

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function setDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }
}
