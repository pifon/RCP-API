<?php

namespace App\Repositories\v1;

use App\DBAL\ServiceEntityRepository;
use App\DTOs\IngredientDTO;
use App\DTOs\IngredientNoteDTO;
use App\DTOs\MeasureDTO;
use App\DTOs\ProductDTO;
use App\Http\Controllers\v1\Recipe\Ingredients;
use Doctrine\ORM\EntityManager;

/**
 * @extends ServiceEntityRepository<Ingredients>
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Ingredients::class);
    }

    /**
     * Get all ingredients for a given recipe.
     *
     * @return IngredientDTO[]
     */
    public function findByRecipeId(int $recipeId): array
    {
        $qb = $this->createQueryBuilder('i')
            ->select([
                'i',
                's',
                'p',
                'm',
                'bm',
                'n',
            ])
            ->join('i.serving', 's')
            ->join('s.product', 'p')
            ->join('s.measure', 'm')
            ->leftJoin('m.baseMeasure', 'bm')
            ->leftJoin('i.notes', 'n')
            ->where('i.recipe = :recipeId')
            ->setParameter('recipeId', $recipeId);

        $rows = $qb->getQuery()->getResult();

        $ingredients = [];

        foreach ($rows as $ingredientEntity) {
            // Map notes
            $notes = [];
            foreach ($ingredientEntity->getNotes() as $noteEntity) {
                $notes[] = new IngredientNoteDTO(
                    id: $noteEntity->getId(),
                    note: $noteEntity->getNote()
                );
            }

            // Map base measure
            $baseMeasureEntity = $ingredientEntity->getServing()->getMeasure()->getBaseMeasure();
            $baseMeasure = null;
            if ($baseMeasureEntity) {
                $baseMeasure = new MeasureDTO(
                    id: $baseMeasureEntity->getId(),
                    name: $baseMeasureEntity->getName(),
                    symbol: $baseMeasureEntity->getSymbol(),
                    type: $baseMeasureEntity->getType(),
                    baseMeasure: null,
                    isBaseMeasure: $baseMeasureEntity->getIsBaseMeasure(),
                    factor: $baseMeasureEntity->getFactor()
                );
            }

            $measureEntity = $ingredientEntity->getServing()->getMeasure();
            $measure = new MeasureDTO(
                id: $measureEntity->getId(),
                name: $measureEntity->getName(),
                symbol: $measureEntity->getSymbol(),
                type: $measureEntity->getType(),
                baseMeasure: $baseMeasure,
                isBaseMeasure: $measureEntity->getIsBaseMeasure(),
                factor: $measureEntity->getFactor()
            );

            $productEntity = $ingredientEntity->getServing()->getProduct();
            $product = new ProductDTO(
                id: $productEntity->getId(),
                name: $productEntity->getName(),
                slug: $productEntity->getSlug(),
                description: $productEntity->getDescription(),
                isVegan: $productEntity->getIsVegan(),
                isVegetarian: $productEntity->getIsVegetarian(),
                isHalal: $productEntity->getIsHalal(),
                isKosher: $productEntity->getIsKosher(),
                isAllergen: $productEntity->getIsAllergen()
            );

            $ingredients[] = new IngredientDTO(
                id: $ingredientEntity->getId(),
                recipeId: $ingredientEntity->getRecipe()->getId(),
                product: $product,
                measure: $measure,
                amount: $ingredientEntity->getServing()->getAmount(),
                notes: $notes
            );
        }

        return $ingredients;
    }
}
