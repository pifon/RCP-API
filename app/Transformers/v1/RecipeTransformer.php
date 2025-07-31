<?php

namespace App\Transformers\v1;

use App\Entities\Recipe;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Psalm\Internal\Json\Json;

class RecipeTransformer extends TransformerAbstract
{
    /**
     * {@inheritDoc}
     */
    public function transform(mixed $item): array
    {
        return [
            "data" => [
                "type" => "recipe",
                "id"=> $item->getId(),
                "attributes" => [
                    "title" => $item->getTitle(),
                    "description" => $item->getDescription(),
                    //"dish-type" => $item->getDishType(),
                    "ingredients" => [], //$item->getIngredients(),
                    "steps" => [] //$item->getSteps()
                ],
                "relationships" => [
                    "author" => $item->getAuthor(), // object containing links and data
                    "cuisine" => [] //$item->getCuisines()
                ]
            ],
            "links" => [
                "self" => route('recipes.show', ['slug' => $item->getSlug()]),
                "describedby" => route('recipes.details', ['slug' => $item->getSlug()])
            ],
            "included"  => [
                $this->transformToJson($item->getAuthor()),
                $this->transformToJson($item->getCuisine()),
                // Uncomment and implement when ingredients are available
                // ...$this->transformIngredientsToJson($item->getIngredients()),
            ]
        ];
    }
    

    

    
    /**
     * Transform ingredient object to JSON format for inclusion
     */
    public function transformIngredientToJson($ingredient): array
    {
        return [
            "type" => "ingredient",
            "id" => $ingredient->getId(),
            "attributes" => [
                "name" => $ingredient->getName(),
                "units" => $ingredient->getUnits(),
                "amount" => $ingredient->getAmount(),
            ],
            "links" => [
                "self" => route('recipes.show', ['slug' => $ingredient->getSlug()]),
            ]
        ];
    }
    
    /**
     * Transform a collection of ingredients to JSON format for inclusion
     */
    public function transformIngredientsToJson($ingredients): array
    {
        $result = [];
        foreach ($ingredients as $ingredient) {
            $result[] = $this->transformIngredientToJson($ingredient);
        }
        return $result;
    }

    public function transformDetailed(mixed $item): array
    {
        return [
            'title' => $item->getTitle(),
            'details' => $item->getDescription(),
            'author' => $item->getAuthor(),
            // 'variant' => $item->getVariant(),
            'created_at' => $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
            '_links' => $this->getDetailedLinks($item),
        ];
    }

    private function getDetailedLinks(mixed $item): array
    {
        return [
            'self' => route('recipes.details', ['slug' => $item->getSlug()]),
            'handle' => route('recipes.show', ['slug' => $item->getSlug()]),
            // 'ingredients' => route('author.recipes', ['username' => $item->getUsername()]),
            // 'procedures' => route('author.cuisines', ['username' => $item->getUsername()]),
            // 'cuisine' => route('author.cuisines', ['username' => $item->getUsername()]),
            // 'related' => route('author.related', ['username' => $item->getUsername()]),
        ];
    }
}
