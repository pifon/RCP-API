<?php

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\DTOs\IngredientDTO;
use App\DTOs\IngredientNoteDTO;
use App\DTOs\MeasureDTO;
use App\DTOs\ProductDTO;
use App\Entities\Ingredient;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<Ingredient>
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Ingredient::class);
    }

    /**
     * Get all ingredients for a given recipe.
     *
     * @return IngredientDTO[]
     */
    public function findByRecipeId(int $recipeId): array
    {
        $qb = $this->createQueryBuilder('i')
            ->join('i.serving', 's')
            ->join('s.product', 'p')
            ->join('s.measure', 'm')
            ->leftJoin('i.notes', 'inote') // zmieniony alias
            ->addSelect(['i', 's', 'p', 'm', 'inote'])
            ->where('i.recipe = :recipeId')
            ->setParameter('recipeId', $recipeId);

        $rows = $qb->getQuery()->getResult();

        $ingredients = [];

        foreach ($rows as $ingredientEntity) {
            $serving = $ingredientEntity->getServing();

            $measureDTO = new MeasureDTO(
                id: $serving->getMeasure()->getId(),
                symbol: $serving->getMeasure()->getSymbol()
            );

            $product = $serving->getProduct();
            $productDTO = new ProductDTO(
                id: $product->getSlug(),
                name: $product->getName()
            );

            // Pobieramy notatki
            $notes = [];
            foreach ($ingredientEntity->getNotes() as $noteEntity) {
                $notes[] = new IngredientNoteDTO(note: $noteEntity->getNote());
            }

            $ingredients[] = new IngredientDTO(
                product: $productDTO,
                amount: $serving->getAmount(),
                measure: $measureDTO,
                notes: $notes
            );
        }

        return $ingredients;
    }
}
