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

class Update extends Controller
{
    public function __construct(
        private readonly PantryItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

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
            'quantity' => ['sometimes', 'numeric', 'min:0'],
            'expires-at' => ['sometimes', 'nullable', 'date'],
            'best-before' => ['sometimes', 'nullable', 'date'],
            'opened-at' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        if (isset($attrs['quantity'])) {
            $oldQty = (float) $item->getQuantity();
            $newQty = (float) $attrs['quantity'];
            $item->setQuantity(number_format($newQty, 3, '.', ''));

            $delta = $newQty - $oldQty;
            if (abs($delta) > 0.0005) {
                $log = new PantryLog();
                $log->setUser($user);
                $log->setPantryItem($item);
                $log->setProduct($item->getProduct());
                $log->setAction('adjusted');
                $log->setQuantityChange(number_format($delta, 3, '.', ''));
                $this->em->persist($log);
            }
        }

        if (array_key_exists('expires-at', $attrs)) {
            $item->setExpiresAt(
                $attrs['expires-at'] !== null ? new \DateTime($attrs['expires-at']) : null
            );
        }

        if (array_key_exists('best-before', $attrs)) {
            $item->setBestBefore(
                $attrs['best-before'] !== null ? new \DateTime($attrs['best-before']) : null
            );
        }

        if (array_key_exists('opened-at', $attrs)) {
            $item->setOpenedAt(
                $attrs['opened-at'] !== null ? new \DateTime($attrs['opened-at']) : null
            );
        }

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $item),
        );
    }
}
