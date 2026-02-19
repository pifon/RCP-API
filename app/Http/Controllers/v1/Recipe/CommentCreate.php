<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\RecipeComment;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Transformers\v1\CommentTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentCreate extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly CommentTransformer $transformer,
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
            'body' => ['required', 'string', 'min:1', 'max:5000'],
            'parent-id' => ['sometimes', 'nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $comment = new RecipeComment();
        $comment->setRecipe($recipe);
        $comment->setUser($user);
        $comment->setBody($attrs['body']);

        if (!empty($attrs['parent-id'])) {
            $parent = $this->em->find(RecipeComment::class, (int) $attrs['parent-id']);
            if ($parent === null) {
                throw new NotFoundException("Parent comment not found.");
            }
            $comment->setParent($parent);
        }

        $this->em->persist($comment);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $comment),
            201,
        );
    }
}
