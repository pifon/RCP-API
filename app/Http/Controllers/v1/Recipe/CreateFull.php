<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Author;
use App\Entities\Direction;
use App\Entities\DirectionIngredient;
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
use App\JsonApi\ErrorObject;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateFull extends Controller
{
    use Concerns\ResolvesCuisine;
    use Concerns\ResolvesProductAndMeasure;

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
        $data = $payload['data'] ?? [];
        $attrs = $data['attributes'] ?? [];
        $rels = $data['relationships'] ?? [];
        $ingredientDefs = $payload['ingredients'] ?? [];
        $directionDefs = $payload['directions'] ?? [];

        $validator = Validator::make($attrs, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'prep-time-minutes' => ['sometimes', 'integer', 'min:0'],
            'cook-time-minutes' => ['sometimes', 'integer', 'min:0'],
            'difficulty' => ['sometimes', 'in:easy,medium,hard,expert'],
            'serves' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:draft,published'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $tempRecipe = new Recipe();
        $cuisineError = $this->applyCuisine($rels, $tempRecipe, $this->em);
        if ($cuisineError !== null) {
            return $cuisineError;
        }

        $user = auth()->user();
        try {
            $author = $this->authorRepository->getAuthor($user);
        } catch (NoResultException) {
            return response()->json(
                Document::errors(new ErrorObject(
                    status: '403',
                    title: 'Author Required',
                    detail: 'You must have an author profile to perform this action.',
                )),
                403,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        }

        $this->em->getConnection()->beginTransaction();

        try {
            $recipe = $this->buildRecipe($attrs, $rels, $tempRecipe, $author);

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

        $ingredientCount = count($ingredientDefs);
        $directionCount = count($directionDefs);

        $doc = Document::single($this->transformer, $recipe);
        $doc['meta'] = [
            'ingredients-created' => $ingredientCount,
            'directions-created' => $directionCount,
        ];

        return response()->json($doc, 201);
    }

    private function buildRecipe(array $attrs, array $rels, Recipe $recipe, Author $author): Recipe
    {
        $title = $attrs['title'];
        $slug = $attrs['slug'] ?? null;

        if ($slug !== null) {
            if ($this->recipeRepository->slugExists($slug)) {
                throw new ValidationErrorException("Slug '{$slug}' already exists.", [
                    'slug' => ["Slug '{$slug}' is already taken."],
                ]);
            }
        } else {
            $slug = $this->generateUniqueSlug($title);
        }

        $recipe->setTitle($title);
        $recipe->setSlug($slug);
        $recipe->setDescription($attrs['description'] ?? null);
        $recipe->setPrepTimeMinutes(isset($attrs['prep-time-minutes']) ? (int) $attrs['prep-time-minutes'] : null);
        $recipe->setCookTimeMinutes(isset($attrs['cook-time-minutes']) ? (int) $attrs['cook-time-minutes'] : null);
        $recipe->setDifficulty($attrs['difficulty'] ?? null);
        $recipe->setServes(isset($attrs['serves']) ? (int) $attrs['serves'] : null);
        $recipe->setStatus($attrs['status'] ?? 'draft');
        $recipe->setAuthor($author);

        if (isset($attrs['source-url'])) {
            $recipe->setSourceUrl($attrs['source-url']);
        }
        if (isset($attrs['source-description'])) {
            $recipe->setSourceDescription($attrs['source-description']);
        }

        $dishTypeId = $rels['dish-type']['data']['id'] ?? null;
        if ($dishTypeId !== null) {
            $dishType = $this->em->getRepository(DishType::class)->find((int) $dishTypeId);
            if ($dishType !== null) {
                $recipe->setDishType($dishType);
            }
        }

        return $recipe;
    }

    /**
     * @return Ingredient[] indexed by position (1-based)
     */
    private function createIngredients(Recipe $recipe, array $defs): array
    {
        $map = [];

        foreach ($defs as $idx => $def) {
            $position = $idx + 1;

            $product = $this->resolveProduct($def);
            $measure = $this->resolveMeasure($def);
            $amount = (float) ($def['amount'] ?? 0);

            if ($amount <= 0) {
                throw new ValidationErrorException(
                    "Ingredient #{$position}: amount is required and must be > 0.",
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
            $ingredient->setPosition($position);
            $ingredient->setOptional((bool) ($def['optional'] ?? false));
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
            $step = $idx + 1;
            $actionName = $def['action'] ?? null;

            if ($actionName === null) {
                throw new ValidationErrorException(
                    "Direction step #{$step}: action is required.",
                    ['directions' => ["Step #{$step} has no action."]],
                );
            }

            $operation = $this->resolveOperation($actionName);

            $serving = null;
            if (isset($def['product-id']) || isset($def['product-slug'])) {
                $product = $this->resolveProduct($def);
                $measure = $this->resolveMeasure($def);
                $amount = (float) ($def['amount'] ?? 0);
                if ($amount <= 0) {
                    throw new ValidationErrorException(
                        "Direction step #{$step}: amount must be greater than zero.",
                        ['directions' => ["Step #{$step} has invalid amount."]],
                    );
                }
                $serving = new Serving();
                $serving->setProduct($product);
                $serving->setAmount($amount);
                $serving->setMeasure($measure);
                $this->em->persist($serving);
            }

            $procedure = new Procedure();
            $procedure->setOperation($operation);
            $procedure->setServing($serving);
            $procedure->setDuration(isset($def['duration-minutes']) ? (int) $def['duration-minutes'] : null);
            $this->em->persist($procedure);

            $direction = new Direction();
            $direction->setRecipe($recipe);
            $direction->setProcedure($procedure);
            $direction->setSequence($step);
            $this->em->persist($direction);

            $ingredientRefs = isset($def['ingredients']) && is_array($def['ingredients'])
                ? $def['ingredients']
                : (isset($def['ingredient']) ? [$def['ingredient']] : []);
            $stepServing = $procedure->getServing();
            foreach ($ingredientRefs as $ref) {
                $ingredient = $ingredientMap[(int) $ref] ?? null;
                if ($ingredient !== null && $stepServing !== null) {
                    $di = new DirectionIngredient();
                    $di->setDirection($direction);
                    $di->setIngredient($ingredient);
                    $di->setServing($stepServing);
                    $direction->getDirectionIngredients()->add($di);
                    $this->em->persist($di);
                }
            }

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

    private function resolveProduct(array $def): Product
    {
        $ref = $def['product-slug'] ?? $def['product-id'] ?? null;
        if ($ref === null || $ref === '') {
            throw new ValidationErrorException(
                'Product is required. Provide product-id or product-slug.',
                ['product-id' => ['Missing product-id or product-slug.']],
            );
        }

        $context = null;
        if (isset($def['amount'], $def['measure-slug'])) {
            $context = ['amount' => (float) $def['amount'], 'measure-slug' => (string) $def['measure-slug']];
        } elseif (isset($def['amount'], $def['measure-id'])) {
            $context = ['amount' => (float) $def['amount'], 'measure-slug' => (string) $def['measure-id']];
        }
        return $this->resolveProductByIdOrSlug($this->em, $ref, $context);
    }

    private function resolveMeasure(array $def): Measure
    {
        $ref = $def['measure-slug'] ?? $def['measure-id'] ?? null;
        if ($ref === null || $ref === '') {
            throw new ValidationErrorException(
                'Measure is required. Provide measure-id or measure-slug.',
                ['measure-id' => ['Missing measure-id or measure-slug.']],
            );
        }

        return $this->resolveMeasureByIdOrSlug($this->em, $ref);
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

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while ($this->recipeRepository->slugExists($slug)) {
            $slug = "{$original}-{$counter}";
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
