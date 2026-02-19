<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Ingredient;
use App\Entities\Measure;
use App\Entities\Product;
use App\Entities\Serving;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\IngredientTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IngredientAdd extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly IngredientTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found");
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];
        $rels = $data['relationships'] ?? [];

        $validator = Validator::make($attrs, [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'notes' => ['sometimes', 'array'],
            'notes.*' => ['string'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $productId = $rels['product']['data']['id'] ?? null;
        $measureId = $rels['measure']['data']['id'] ?? null;

        if ($productId === null || $measureId === null) {
            throw new ValidationErrorException(
                'Product and measure are required.',
                ['relationships' => ['Provide relationships.product and relationships.measure.']],
            );
        }

        $product = $this->em->find(Product::class, (int) $productId);
        if ($product === null) {
            throw new NotFoundException("Product '{$productId}' not found.");
        }

        $measure = $this->em->find(Measure::class, (int) $measureId);
        if ($measure === null) {
            throw new NotFoundException("Measure '{$measureId}' not found.");
        }

        $serving = new Serving();
        $serving->setProduct($product);
        $serving->setAmount((float) $attrs['amount']);
        $serving->setMeasure($measure);
        $this->em->persist($serving);

        $maxPos = $this->em->getConnection()->fetchOne(
            'SELECT COALESCE(MAX(position), 0) FROM ingredients WHERE recipe_id = ?',
            [$recipe->getId()],
        );

        $ingredient = new Ingredient();
        $ingredient->setRecipe($recipe);
        $ingredient->setServing($serving);
        $ingredient->setPosition((int) ($attrs['position'] ?? ((int) $maxPos + 1)));
        $this->em->persist($ingredient);

        if (!empty($attrs['notes'])) {
            foreach ($attrs['notes'] as $text) {
                $note = new \App\Entities\IngredientNote();
                $this->setNoteFields($note, $ingredient, $text);
                $this->em->persist($note);
            }
        }

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $ingredient),
            201,
        );
    }

    private function setNoteFields(\App\Entities\IngredientNote $note, Ingredient $ingredient, string $text): void
    {
        $ref = new \ReflectionClass($note);

        $prop = $ref->getProperty('ingredient');
        $prop->setValue($note, $ingredient);

        $prop = $ref->getProperty('note');
        $prop->setValue($note, $text);

        $prop = $ref->getProperty('createdAt');
        $prop->setValue($note, new \DateTime());

        $prop = $ref->getProperty('updatedAt');
        $prop->setValue($note, new \DateTime());
    }
}
