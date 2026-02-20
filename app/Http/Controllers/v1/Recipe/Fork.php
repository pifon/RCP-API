<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Direction;
use App\Entities\DirectionNote;
use App\Entities\Ingredient;
use App\Entities\IngredientNote;
use App\Entities\Procedure;
use App\Entities\Recipe;
use App\Entities\Serving;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Fork extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly RecipeTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $original = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $user = auth()->user();
        $author = $this->authorRepository->getAuthor($user);

        $this->em->getConnection()->beginTransaction();

        try {
            $fork = $this->cloneRecipe($original, $author);
            $this->em->persist($fork);
            $this->em->flush();

            $ingredientMap = $this->cloneIngredients($original, $fork);
            $this->cloneDirections($original, $fork, $ingredientMap);

            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        $this->em->refresh($fork);

        $doc = Document::single($this->transformer, $fork);
        $doc['meta'] = [
            'forked-from' => $original->getSlug(),
            'original-author' => $original->getAuthor()->getName(),
            'ingredients-cloned' => count($ingredientMap),
            'directions-cloned' => $original->getDirections()->count(),
        ];

        return response()->json($doc, 201);
    }

    private function cloneRecipe(Recipe $source, \App\Entities\Author $newAuthor): Recipe
    {
        $fork = new Recipe;
        $fork->setTitle($source->getTitle());
        $fork->setSlug($this->generateUniqueSlug($source->getSlug()));
        $fork->setDescription($source->getDescription());
        $fork->setPrepTimeMinutes($source->getPrepTimeMinutes());
        $fork->setCookTimeMinutes($source->getCookTimeMinutes());
        $fork->setDifficulty($source->getDifficulty());
        $fork->setServes($source->getServes());
        $fork->setStatus('draft');
        $fork->setAuthor($newAuthor);
        $fork->setForkedFrom($source);
        $fork->setCuisine($source->getCuisine());
        $fork->setDishType($source->getDishType());

        if ($source->getSourceUrl() !== null) {
            $fork->setSourceUrl($source->getSourceUrl());
        }
        if ($source->getSourceDescription() !== null) {
            $fork->setSourceDescription($source->getSourceDescription());
        }

        return $fork;
    }

    /**
     * @return array<int, Ingredient> old ingredient ID → new Ingredient
     */
    private function cloneIngredients(Recipe $source, Recipe $fork): array
    {
        $map = [];

        /** @var Ingredient $original */
        foreach ($source->getIngredients() as $original) {
            $srcServing = $original->getServing();

            $newServing = new Serving;
            $newServing->setProduct($srcServing->getProduct());
            $newServing->setAmount($srcServing->getAmount());
            $newServing->setMeasure($srcServing->getMeasure());
            $this->em->persist($newServing);

            $newIngredient = new Ingredient;
            $newIngredient->setRecipe($fork);
            $newIngredient->setServing($newServing);
            $newIngredient->setPosition($original->getPosition());
            $this->em->persist($newIngredient);

            foreach ($original->getNotes() as $srcNote) {
                $note = new IngredientNote;
                $this->setPrivateFields($note, [
                    'ingredient' => $newIngredient,
                    'note' => $srcNote->getNote(),
                    'createdAt' => new \DateTime,
                    'updatedAt' => new \DateTime,
                ]);
                $this->em->persist($note);
            }

            $map[$original->getId()] = $newIngredient;
        }

        return $map;
    }

    private function cloneDirections(Recipe $source, Recipe $fork, array $ingredientMap): void
    {
        /** @var Direction $original */
        foreach ($source->getDirections() as $original) {
            $srcProcedure = $original->getProcedure();

            $newProcServing = null;
            if ($srcProcedure->getServing() !== null) {
                $s = $srcProcedure->getServing();
                $newProcServing = new Serving;
                $newProcServing->setProduct($s->getProduct());
                $newProcServing->setAmount($s->getAmount());
                $newProcServing->setMeasure($s->getMeasure());
                $this->em->persist($newProcServing);
            }

            $newProcedure = new Procedure;
            $newProcedure->setOperation($srcProcedure->getOperation());
            $newProcedure->setServing($newProcServing);
            $newProcedure->setDuration($srcProcedure->getDuration());
            $this->em->persist($newProcedure);

            $linkedIngredient = null;
            $srcIngredient = $original->getIngredient();
            if ($srcIngredient !== null) {
                $linkedIngredient = $ingredientMap[$srcIngredient->getId()] ?? null;
            }

            $newDirection = new Direction;
            $newDirection->setRecipe($fork);
            $newDirection->setProcedure($newProcedure);
            $newDirection->setIngredient($linkedIngredient);
            $newDirection->setSequence($original->getSequence());
            $this->em->persist($newDirection);

            foreach ($original->getNotes() as $srcNote) {
                $note = new DirectionNote;
                $note->setDirection($newDirection);
                $note->setNote($srcNote->getNote());
                $this->em->persist($note);
            }
        }
    }

    private function generateUniqueSlug(string $base): string
    {
        $slug = $base.'-fork';
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
