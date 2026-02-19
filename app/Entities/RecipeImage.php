<?php

declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'recipe_images')]
#[ORM\Entity]
class RecipeImage
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Recipe $recipe;

    #[ORM\Column(name: 'path', type: 'string', length: 255, nullable: false)]
    private string $path;

    #[ORM\Column(name: 'type', type: 'string', length: 20, nullable: false, options: ['default' => 'gallery'])]
    private string $type = 'gallery';

    #[ORM\Column(name: 'position', type: 'smallint', nullable: false, options: ['unsigned' => true, 'default' => 0])]
    private int $position = 0;

    #[ORM\Column(name: 'alt_text', type: 'string', length: 255, nullable: true)]
    private ?string $altText = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }
}
