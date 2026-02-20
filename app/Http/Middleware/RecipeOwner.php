<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\v1\NotFoundException;
use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\RecipeRepository;
use Closure;
use Illuminate\Http\Request;

class RecipeOwner
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly AuthorRepository $authorRepository,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $slug = $request->route('slug');

        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found.");
        }

        $user = $request->user();
        if ($user === null) {
            return response()->json(
                Document::errors(new ErrorObject('401', 'Unauthorized', 'Authentication required.')),
                401,
            );
        }

        try {
            $author = $this->authorRepository->getAuthor($user);
        } catch (\Throwable) {
            return response()->json(
                Document::errors(new ErrorObject('403', 'Forbidden', 'You are not an author.')),
                403,
            );
        }

        if ($recipe->getAuthor()->getId() !== $author->getId()) {
            return response()->json(
                Document::errors(new ErrorObject('403', 'Forbidden', 'You are not the owner of this recipe.')),
                403,
            );
        }

        return $next($request);
    }
}
