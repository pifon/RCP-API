<?php

namespace App\Entities;

use App\Enums\MeasureType;
use App\Repositories\v1\MeasureRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(name: 'abbr', type: 'string', length: 255, nullable: true)]
    private string $abbr;

    #[ORM\Column(name: 'measure_type', type: TYPES::STRING, length: 1, nullable: false)]
    private MeasureType $measureType;

    #[ORM\JoinColumn(name: 'base_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Measure::class)]
    private Measure $baseMeasure;

    #[ORM\Column(name: 'factor', type: 'float', nullable: false, options: ['unsigned' => true])]
    private float $factor;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;
}
