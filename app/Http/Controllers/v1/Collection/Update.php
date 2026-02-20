<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Collection;

use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\CollectionRepository;
use App\Transformers\v1\CollectionTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Update extends Controller
{
    public function __construct(
        private readonly CollectionRepository $repository,
        private readonly CollectionTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $collection = $this->repository->findByIdForUser($id, $user);
        if ($collection === null) {
            throw new NotFoundException('Collection not found.');
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is-public' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        if (isset($attrs['name'])) {
            $collection->setName($attrs['name']);
        }

        if (array_key_exists('description', $attrs)) {
            $collection->setDescription($attrs['description']);
        }

        if (isset($attrs['is-public'])) {
            $collection->setIsPublic((bool) $attrs['is-public']);
        }

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $collection),
        );
    }
}
