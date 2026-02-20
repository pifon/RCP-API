<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\CuisineRequest;

use App\Entities\CuisineRequest;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\AuthorRepository;
use App\Transformers\v1\CuisineRequestTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Create extends Controller
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly CuisineRequestTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $attrs = $request->input('data.attributes', []);

        $requestValidator = Validator::make($attrs, [
            'name' => ['required', 'string', 'max:255'],
            'variant' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($requestValidator->fails()) {
            throw ValidationErrorException::fromValidationBag($requestValidator->errors());
        }

        $user = auth()->user();
        $author = $this->authorRepository->getAuthor($user);

        $cuisineRequest = new CuisineRequest();
        $cuisineRequest->setName($attrs['name']);
        $cuisineRequest->setVariant($attrs['variant'] ?? null);
        $cuisineRequest->setDescription($attrs['description'] ?? null);
        $cuisineRequest->setRequestedBy($author);

        $this->em->persist($cuisineRequest);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $cuisineRequest),
            201,
        );
    }
}
