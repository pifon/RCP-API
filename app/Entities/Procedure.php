<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'procedures')]
#[ORM\Entity]
class Procedure
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Serving::class)]
    #[ORM\JoinColumn(name: 'serving_id', referencedColumnName: 'id', nullable: true)]
    private ?Serving $serving = null;

    #[ORM\ManyToOne(targetEntity: Operation::class)]
    #[ORM\JoinColumn(name: 'operation_id', referencedColumnName: 'id', nullable: false)]
    private Operation $operation;

    #[ORM\Column(name: 'duration', type: 'integer', nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getServing(): ?Serving
    {
        return $this->serving;
    }

    public function setServing(?Serving $serving): void
    {
        $this->serving = $serving;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function setOperation(Operation $operation): void
    {
        $this->operation = $operation;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $minutes): void
    {
        $this->duration = $minutes;
    }
}
