<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\RecipeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'recipes')]
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, nullable: false)]
    private string $slug;

    #[ORM\OneToMany(mappedBy: 'variant', targetEntity: Recipe::class)]
    private Recipe $variant;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private Author $author;

    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: Cuisine::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private Cuisine $cuisine;

    #[ORM\ManyToOne(targetEntity: DishType::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private DishType $type;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

}