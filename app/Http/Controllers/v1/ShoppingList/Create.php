<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\ShoppingList;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\ShoppingListTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Create extends Controller
{
    public function __construct(
        private readonly ShoppingListTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,completed,archived'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $list = new ShoppingList();
        $list->setUser($user);
        $list->setName($attrs['name']);

        if (isset($attrs['status'])) {
            $list->setStatus($attrs['status']);
        }

        $collectionRef = $data['relationships']['collection']['data']['id'] ?? null;
        if ($collectionRef !== null) {
            $collection = $this->em->find(\App\Entities\Collection::class, (int) $collectionRef);
            $list->setCollection($collection);
        }

        $this->em->persist($list);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $list),
            201,
        );
    }
}
