<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Pantry;

use App\Entities\PantryItem;
use App\Entities\PantryLog;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\PantryItemTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Consume extends Controller
{
    public function __construct(
        private readonly PantryItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, int $id): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $item = $this->em->getRepository(PantryItem::class)->findOneBy([
            'id' => $id,
            'user' => $user,
        ]);

        if ($item === null) {
            throw new NotFoundException('Pantry item not found.');
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $consumeQty = (float) $attrs['quantity'];
        $item->adjustQuantity(-$consumeQty);

        $log = new PantryLog;
        $log->setUser($user);
        $log->setPantryItem($item);
        $log->setProduct($item->getProduct());
        $log->setAction('consumed');
        $log->setQuantityChange(number_format(-$consumeQty, 3, '.', ''));
        $log->setNote($attrs['note'] ?? null);
        $this->em->persist($log);

        if ((float) $item->getQuantity() <= 0) {
            $this->em->remove($item);
            $this->em->flush();

            return response()->json(
                Document::meta([
                    'message' => 'Item fully consumed and removed from pantry.',
                ]),
            );
        }

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $item),
        );
    }
}
