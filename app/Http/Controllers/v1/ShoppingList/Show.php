<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\ShoppingList;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\ShoppingListTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly ShoppingListTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $list = $this->em->getRepository(ShoppingList::class)->findOneBy([
            'id' => $id,
            'user' => $user,
        ]);

        if ($list === null) {
            throw new NotFoundException('Shopping list not found.');
        }

        $params = QueryParameters::fromArray($request->query->all());

        return response()->json(
            Document::single($this->transformer, $list, $params),
        );
    }
}
