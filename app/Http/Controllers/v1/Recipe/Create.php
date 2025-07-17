<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Entities\Recipe;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\RecipeResource;
use App\Repositories\v1\AuthorRepository;
use App\Repositories\v1\RecipeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Create extends Controller
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly RecipeRepository $recipeRepository,
    ) {}

    /**
     * @throws ValidationErrorException
     */
    public function __invoke(Request $request): RecipeResource
    {
        $user = auth()->user();
        $author = $this->authorRepository->getAuthor($user);
        $requestValidator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['sometimes', 'string'],
        ], [
            'title.required' => 'Title is required',
        ]);

        if ($requestValidator->fails()) {
            throw ValidationErrorException::fromValidationBag($requestValidator->errors());
        }

        $title = $request->get('title');
        $slug = $this->generateUniqueSlug($title);

        $recipe = new Recipe;
        $recipe->setTitle($title);
        $recipe->setSlug($slug);
        $recipe->setDescription((string) $request->get('description', ''));
        $recipe->setAuthor($author);
        $recipe->setCreatedAt();
        $recipe->setUpdatedAt();

        return new RecipeResource($recipe);
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title); // e.g., "My Recipe Title" => "my-recipe-title"
        $originalSlug = $slug;
        $counter = 1;

        while ($this->recipeRepository->slugExists($slug)) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
