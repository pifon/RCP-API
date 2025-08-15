<?php

namespace App\Entities;

use App\Repositories\v1\IngredientRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ingredients')]
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    private Recipe $recipe;

    #[ORM\JoinColumn(name: 'serving_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Serving::class)]
    private Serving $serving;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;
}
