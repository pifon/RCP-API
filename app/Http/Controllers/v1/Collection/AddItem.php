<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Entities\CollectionItem;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\CollectionRepository;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\CollectionItemTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddItem extends Controller
{
    public function __construct(
        private readonly CollectionRepository $collectionRepository,
        private readonly RecipeRepository $recipeRepository,
        private readonly CollectionItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, int $collectionId): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $collection = $this->collectionRepository->findByIdForUser($collectionId, $user);
        if ($collection === null) {
            throw new NotFoundException("Collection not found.");
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $recipeRef = $data['relationships']['recipe']['data']['id'] ?? ($attrs['recipe-slug'] ?? null);

        if ($recipeRef === null) {
            throw new ValidationErrorException(
                'Recipe reference is required.',
                ['recipe' => ['Provide relationships.recipe.data.id (slug).']],
            );
        }

        try {
            $recipe = $this->recipeRepository->getRecipe($recipeRef);
        } catch (NoResultException) {
            throw new NotFoundException("Recipe '{$recipeRef}' not found.");
        }

        $validator = Validator::make($attrs, [
            'position' => ['sometimes', 'integer', 'min:0'],
            'scheduled-date' => ['sometimes', 'nullable', 'date'],
            'meal-slot' => ['sometimes', 'nullable', 'string', 'max:20'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $item = new CollectionItem();
        $item->setCollection($collection);
        $item->setRecipe($recipe);
        $item->setPosition((int) ($attrs['position'] ?? 0));

        if (isset($attrs['scheduled-date'])) {
            $item->setScheduledDate(new \DateTime($attrs['scheduled-date']));
        }

        if (isset($attrs['meal-slot'])) {
            $item->setMealSlot($attrs['meal-slot']);
        }

        if (isset($attrs['note'])) {
            $item->setNote($attrs['note']);
        }

        $this->em->persist($item);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $item),
            201,
        );
    }
}
