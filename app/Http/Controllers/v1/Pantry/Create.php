<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Pantry;

use App\Entities\Measure;
use App\Entities\PantryItem;
use App\Entities\PantryLog;
use App\Entities\Product;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\PantryItemTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Create extends Controller
{
    public function __construct(
        private readonly PantryItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $productRef = $data['relationships']['product']['data']['id']
            ?? ($attrs['product-id'] ?? null);

        if ($productRef === null) {
            throw new ValidationErrorException(
                'Product reference is required.',
                ['product' => ['Provide relationships.product.data.id.']],
            );
        }

        $product = $this->em->find(Product::class, (int) $productRef);
        if ($product === null) {
            throw new NotFoundException('Product not found.');
        }

        $validator = Validator::make($attrs, [
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'expires-at' => ['sometimes', 'nullable', 'date'],
            'best-before' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $item = new PantryItem;
        $item->setUser($user);
        $item->setProduct($product);
        $item->setQuantity(number_format((float) $attrs['quantity'], 3, '.', ''));

        $measureRef = $data['relationships']['measure']['data']['id'] ?? null;
        if ($measureRef !== null) {
            $measure = $this->em->find(Measure::class, (int) $measureRef);
            $item->setMeasure($measure);
        }

        if (isset($attrs['expires-at'])) {
            $item->setExpiresAt(new \DateTime($attrs['expires-at']));
        }

        if (isset($attrs['best-before'])) {
            $item->setBestBefore(new \DateTime($attrs['best-before']));
        }

        $this->em->persist($item);

        $log = new PantryLog;
        $log->setUser($user);
        $log->setPantryItem($item);
        $log->setProduct($product);
        $log->setAction('added');
        $log->setQuantityChange(number_format((float) $attrs['quantity'], 3, '.', ''));
        $this->em->persist($log);

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $item),
            201,
        );
    }
}
