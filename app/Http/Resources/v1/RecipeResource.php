<?php

namespace App\Http\Resources\v1;

use App\Entities\Recipe;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        /** @var Recipe $recipe */
        $recipe = $this->resource;

        return [
            'title' => $recipe->getTitle(),
            'details' => $recipe->getDescription(),
            'author' => $recipe->getAuthor(),
            // 'variant' => $this->getVariant(),
            'created_at' => $recipe->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $recipe->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => [
                'self' => route('recipes.show', ['slug' => $recipe->getSlug()]),
                'details' => route('recipes.details', ['slug' => $recipe->getSlug()]),
                'ingredients' => route('recipes.ingredients.show', ['slug' => $recipe->getSlug()]),
            ],
        ];
    }
}
