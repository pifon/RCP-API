<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\DishType;
use App\Entities\Recipe;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Recipe\Concerns\ResolvesCuisine;
use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\Repositories\v1\AuthorRepository;
use Doctrine\ORM\NoResultException;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Create extends Controller
{
    use ResolvesCuisine;

    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];
        $rels = $data['relationships'] ?? [];

        $requestValidator = Validator::make($attrs, [
            'title' => ['required', 'string'],
            'description' => ['sometimes', 'string'],
            'prep-time-minutes' => ['sometimes', 'integer', 'min:0'],
            'cook-time-minutes' => ['sometimes', 'integer', 'min:0'],
            'difficulty' => ['sometimes', 'in:easy,medium,hard,expert'],
            'serves' => ['sometimes', 'integer', 'min:1'],
        ]);

        if ($requestValidator->fails()) {
            throw ValidationErrorException::fromValidationBag($requestValidator->errors());
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

        $title = $attrs['title'];
        $slug = $this->generateUniqueSlug($title);

        $recipe = new Recipe();
        $recipe->setTitle($title);
        $recipe->setSlug($slug);
        $recipe->setDescription($attrs['description'] ?? null);
        $recipe->setPrepTimeMinutes(isset($attrs['prep-time-minutes']) ? (int) $attrs['prep-time-minutes'] : null);
        $recipe->setCookTimeMinutes(isset($attrs['cook-time-minutes']) ? (int) $attrs['cook-time-minutes'] : null);
        $recipe->setDifficulty($attrs['difficulty'] ?? null);
        $recipe->setServes(isset($attrs['serves']) ? (int) $attrs['serves'] : null);
        $recipe->setAuthor($author);

        $cuisineError = $this->applyCuisine($rels, $recipe, $this->em);
        if ($cuisineError !== null) {
            return $cuisineError;
        }

        $dishTypeId = $rels['dish-type']['data']['id'] ?? null;
        if ($dishTypeId !== null) {
            $dishType = $this->em->find(DishType::class, (int) $dishTypeId);
            if ($dishType !== null) {
                $recipe->setDishType($dishType);
            }
        }

        $this->em->persist($recipe);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $recipe),
            201,
        );
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
}
