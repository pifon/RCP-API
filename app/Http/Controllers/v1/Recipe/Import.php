<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
use App\Entities\DirectionNote;
use App\Entities\DishType;
use App\Entities\Ingredient;
use App\Entities\IngredientNote;
use App\Entities\Measure;
use App\Entities\Operation;
use App\Entities\Procedure;
use App\Entities\Product;
use App\Entities\Recipe;
use App\Entities\Serving;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Import extends Controller
{
    use Concerns\ResolvesCuisine;

    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! isset($payload['pifon-recipe'])) {
            throw new ValidationErrorException(
                'Invalid recipe file: missing pifon-recipe version header.',
                ['pifon-recipe' => ['Missing format version.']],
            );
        }

        $recipeDef = $payload['recipe'] ?? [];
        $ingredientDefs = $payload['ingredients'] ?? [];
        $directionDefs = $payload['directions'] ?? [];

        if (empty($recipeDef['title'])) {
            throw new ValidationErrorException(
                'Recipe title is required.',
                ['title' => ['Missing recipe title.']],
            );
        }

        $this->em->getConnection()->beginTransaction();

        try {
            $recipe = $this->buildRecipe($recipeDef);
            $this->em->persist($recipe);
            $this->em->flush();

            $ingredientMap = $this->createIngredients($recipe, $ingredientDefs);
            $this->createDirections($recipe, $directionDefs, $ingredientMap);

            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        $this->em->refresh($recipe);

        $doc = Document::single($this->transformer, $recipe);
        $doc['meta'] = [
            'imported' => true,
            'source-format' => $payload['pifon-recipe'],
            'ingredients-created' => count($ingredientDefs),
            'directions-created' => count($directionDefs),
        ];

        return response()->json($doc, 201);
    }

    private function buildRecipe(array $def): Recipe
    {
        $user = auth()->user();
        $author = $this->authorRepository->getAuthor($user);

        $title = $def['title'];
        $slug = $def['slug'] ?? null;

        if ($slug !== null && $this->recipeRepository->slugExists($slug)) {
            $slug = $this->generateUniqueSlug($slug);
        } elseif ($slug === null) {
            $slug = $this->generateUniqueSlug(Str::slug($title));
        }

        $recipe = new Recipe();
        $recipe->setTitle($title);
        $recipe->setSlug($slug);
        $recipe->setDescription($def['description'] ?? null);
        $recipe->setPrepTimeMinutes(isset($def['prep-time-minutes']) ? (int) $def['prep-time-minutes'] : null);
        $recipe->setCookTimeMinutes(isset($def['cook-time-minutes']) ? (int) $def['cook-time-minutes'] : null);
        $recipe->setDifficulty($def['difficulty'] ?? null);
        $recipe->setServes(isset($def['serves']) ? (int) $def['serves'] : null);
        $recipe->setStatus($def['status'] ?? 'draft');
        $recipe->setAuthor($author);

        if (isset($def['source-url'])) {
            $recipe->setSourceUrl($def['source-url']);
        }
        if (isset($def['source-description'])) {
            $recipe->setSourceDescription($def['source-description']);
        }

        $importRels = [];
        if (isset($def['cuisine']['id'])) {
            $importRels['cuisine'] = ['data' => ['id' => (int) $def['cuisine']['id']]];
        }
        if (isset($def['cuisine-request-id'])) {
            $importRels['cuisine-request'] = ['data' => ['id' => (int) $def['cuisine-request-id']]];
        }
        $cuisineError = $this->applyCuisine($importRels, $recipe, $this->em);
        if ($cuisineError !== null) {
            throw new ValidationErrorException(
                'Cuisine is required in imported recipe.',
                ['cuisine' => ['Provide an existing cuisine or a cuisine-request.']],
            );
        }

        if (isset($def['dish-type']['id'])) {
            $dishType = $this->em->find(DishType::class, (int) $def['dish-type']['id']);
            if ($dishType !== null) {
                $recipe->setDishType($dishType);
            }
        }

        return $recipe;
    }

    /**
     * @return Ingredient[] keyed by position
     */
    private function createIngredients(Recipe $recipe, array $defs): array
    {
        $map = [];

        foreach ($defs as $idx => $def) {
            $position = $def['position'] ?? ($idx + 1);

            $product = $this->resolveProduct($def, $position);
            $measure = $this->resolveMeasure($def, $position);
            $amount = (float) ($def['amount'] ?? 0);

            if ($amount <= 0) {
                throw new ValidationErrorException(
                    "Ingredient #{$position}: amount must be > 0.",
                    ['ingredients' => ["Item #{$position} has invalid amount."]],
                );
            }

            $serving = new Serving();
            $serving->setProduct($product);
            $serving->setAmount($amount);
            $serving->setMeasure($measure);
            $this->em->persist($serving);

            $ingredient = new Ingredient();
            $ingredient->setRecipe($recipe);
            $ingredient->setServing($serving);
            $ingredient->setPosition((int) $position);
            $this->em->persist($ingredient);

            if (! empty($def['notes'])) {
                foreach ((array) $def['notes'] as $text) {
                    $note = new IngredientNote();
                    $this->setPrivateFields($note, [
                        'ingredient' => $ingredient,
                        'note' => $text,
                        'createdAt' => new \DateTime(),
                        'updatedAt' => new \DateTime(),
                    ]);
                    $this->em->persist($note);
                }
            }

            $map[$position] = $ingredient;
        }

        return $map;
    }

    private function createDirections(Recipe $recipe, array $defs, array $ingredientMap): void
    {
        foreach ($defs as $idx => $def) {
            $step = $def['step'] ?? ($idx + 1);
            $actionName = $def['action'] ?? null;

            if ($actionName === null) {
                throw new ValidationErrorException(
                    "Direction step #{$step}: action is required.",
                    ['directions' => ["Step #{$step} missing action."]],
                );
            }

            $operation = $this->resolveOperation($actionName);

            $serving = null;
            if (isset($def['product-id'])) {
                $product = $this->em->find(Product::class, (int) $def['product-id']);
                if ($product !== null) {
                    $measure = isset($def['measure-id'])
                        ? $this->em->find(Measure::class, (int) $def['measure-id'])
                        : null;
                    $amount = (float) ($def['amount'] ?? 0);

                    if ($measure !== null) {
                        $serving = new Serving();
                        $serving->setProduct($product);
                        $serving->setAmount($amount);
                        $serving->setMeasure($measure);
                        $this->em->persist($serving);
                    }
                }
            }

            $procedure = new Procedure();
            $procedure->setOperation($operation);
            $procedure->setServing($serving);
            $procedure->setDuration(isset($def['duration-minutes']) ? (int) $def['duration-minutes'] : null);
            $this->em->persist($procedure);

            $ingredient = null;
            if (isset($def['ingredient'])) {
                $ingredient = $ingredientMap[(int) $def['ingredient']] ?? null;
            }

            $direction = new Direction();
            $direction->setRecipe($recipe);
            $direction->setProcedure($procedure);
            $direction->setIngredient($ingredient);
            $direction->setSequence((int) $step);
            $this->em->persist($direction);

            if (! empty($def['notes'])) {
                foreach ((array) $def['notes'] as $text) {
                    $note = new DirectionNote();
                    $note->setDirection($direction);
                    $note->setNote($text);
                    $this->em->persist($note);
                }
            }
        }
    }

    private function resolveProduct(array $def, int $position): Product
    {
        $id = $def['product-id'] ?? null;
        if ($id === null) {
            throw new ValidationErrorException(
                "Ingredient #{$position}: product-id is required.",
                ['ingredients' => ["Item #{$position} missing product-id."]],
            );
        }

        $product = $this->em->find(Product::class, (int) $id);
        if ($product === null) {
            throw new NotFoundException("Product #{$id} not found (ingredient #{$position}).");
        }

        return $product;
    }

    private function resolveMeasure(array $def, int $position): Measure
    {
        $id = $def['measure-id'] ?? null;
        if ($id === null) {
            throw new ValidationErrorException(
                "Ingredient #{$position}: measure-id is required.",
                ['ingredients' => ["Item #{$position} missing measure-id."]],
            );
        }

        $measure = $this->em->find(Measure::class, (int) $id);
        if ($measure === null) {
            throw new NotFoundException("Measure #{$id} not found (ingredient #{$position}).");
        }

        return $measure;
    }

    private function resolveOperation(string $name): Operation
    {
        $op = $this->em->getRepository(Operation::class)->findOneBy(['name' => $name]);

        if ($op === null) {
            $op = new Operation();
            $op->setName($name);
            $op->setDescription($name);
            $this->em->persist($op);
        }

        return $op;
    }

    private function generateUniqueSlug(string $base): string
    {
        $slug = $base;
        $counter = 1;

        while ($this->recipeRepository->slugExists($slug)) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function setPrivateFields(object $entity, array $values): void
    {
        $ref = new \ReflectionClass($entity);
        foreach ($values as $prop => $val) {
            $p = $ref->getProperty($prop);
            $p->setValue($entity, $val);
        }
    }
}
