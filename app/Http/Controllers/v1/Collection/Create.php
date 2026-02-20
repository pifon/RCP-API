<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Entities\Collection;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\CollectionRepository;
use App\Transformers\v1\CollectionTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Create extends Controller
{
    public function __construct(
        private readonly CollectionRepository $repository,
        private readonly CollectionTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'type' => ['sometimes', 'in:bag,menu'],
            'is-public' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $slug = Str::slug($attrs['name']);
        $original = $slug;
        $counter = 1;
        while ($this->repository->slugExistsForUser($slug, $user)) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        $collection = new Collection;
        $collection->setUser($user);
        $collection->setName($attrs['name']);
        $collection->setSlug($slug);
        $collection->setDescription($attrs['description'] ?? null);
        $collection->setType($attrs['type'] ?? 'bag');
        $collection->setIsPublic($attrs['is-public'] ?? false);

        $this->em->persist($collection);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $collection),
            201,
        );
    }
}
