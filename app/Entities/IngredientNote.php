<?php

namespace App\Entities;

use App\Repositories\v1\IngredientRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ingredient_notes')]
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class IngredientNote
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'ingredient_id', referencedColumnName: 'id', nullable: false)]
    private Ingredient $ingredient;

    #[ORM\Column(name: 'note', type: 'string', nullable: true)]
    private string $note;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getNote(): string
    {
        return $this->note;

    }
}
