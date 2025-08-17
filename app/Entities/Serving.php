<?php

namespace App\Entities;

use App\Repositories\v1\ServingRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'servings')]
#[ORM\Entity(repositoryClass: ServingRepository::class)]
class Serving
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    #[ORM\Column(name: 'amount', type: 'float', nullable: false, options: ['unsigned' => true])]
    private float $amount;

    #[ORM\JoinColumn(name: 'measure_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Measure::class)]
    private Measure $measure;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime;
        $this->updatedAt = new DateTime;
    }

    public function getMeasure(): Measure
    {
        return $this->measure;
    }

    public function getProduct(): Product
    {
        return $this->product;

    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
