<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\UserRecipeActivity;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogActivity extends Controller
{
    private const ALLOWED_ACTIONS = ['viewed', 'cooked', 'saved', 'shared'];

    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (NoResultException) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'action' => ['required', 'string', 'in:'.implode(',', self::ALLOWED_ACTIONS)],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $activity = new UserRecipeActivity;
        $activity->setUser($user);
        $activity->setRecipe($recipe);
        $activity->setAction($attrs['action']);

        $this->em->persist($activity);
        $this->em->flush();

        return response()->json(
            Document::meta([
                'action' => $attrs['action'],
                'recipe' => $slug,
                'recorded-at' => $activity->getCreatedAt()->format('c'),
            ]),
            201,
        );
    }
}
