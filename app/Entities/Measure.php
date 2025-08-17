<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\MeasureType;
use App\Repositories\v1\MeasureRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'measures')]
#[ORM\Entity(repositoryClass: MeasureRepository::class)]
class Measure
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, nullable: true)]
    private string $slug;

    #[ORM\Column(name: 'measure_type', type: 'string', length: 1, nullable: false)]
    private string $measureType;

    #[ORM\JoinColumn(name: 'base_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Measure::class)]
    private Measure $baseMeasure;

    #[ORM\Column(name: 'factor', type: 'float', nullable: false, options: ['unsigned' => true])]
    private float $factor;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getBaseMeasure(): Measure
    {

        return $this->baseMeasure;
    }

    public function isBaseMeasure(): bool
    {
        return $this->baseMeasure->getId() === $this->getId();
    }

    /**
     * @return MeasureType
     */
    public function getMeasureType(): string
    {
        return $this->measureType;
    }

    public function getSymbol(): string
    {
        return $this->getSlug();
    }

    public function getFactor(): float
    {
        return $this->factor;
    }
}
