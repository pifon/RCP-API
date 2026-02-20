<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Rating;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\RatingTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingCreate extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly RatingTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

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
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $existing = $this->em->getRepository(Rating::class)->findOneBy([
            'recipe' => $recipe,
            'user' => $user,
        ]);

        if ($existing !== null) {
            $existing->setRate((int) $attrs['rate']);
            $this->em->flush();

            return response()->json(
                Document::single($this->transformer, $existing),
            );
        }

        $rating = new Rating();
        $rating->setRecipe($recipe);
        $rating->setUser($user);
        $rating->setRate((int) $attrs['rate']);

        $this->em->persist($rating);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $rating),
            201,
        );
    }
}
