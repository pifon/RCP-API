<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\ShoppingList;

use App\Entities\Measure;
use App\Entities\Product;
use App\Entities\ShoppingList;
use App\Entities\ShoppingListItem;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\ShoppingListItemTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddItem extends Controller
{
    public function __construct(
        private readonly ShoppingListItemTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request, int $listId): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $list = $this->em->getRepository(ShoppingList::class)->findOneBy([
            'id' => $listId,
            'user' => $user,
        ]);

        if ($list === null) {
            throw new NotFoundException('Shopping list not found.');
        }

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
            throw new NotFoundException("Product not found.");
        }

        $validator = Validator::make($attrs, [
            'quantity' => ['sometimes', 'numeric', 'min:0.001'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $item = new ShoppingListItem();
        $item->setShoppingList($list);
        $item->setProduct($product);
        $item->setQuantity(number_format((float) ($attrs['quantity'] ?? 1), 3, '.', ''));

        $measureRef = $data['relationships']['measure']['data']['id'] ?? null;
        if ($measureRef !== null) {
            $measure = $this->em->find(Measure::class, (int) $measureRef);
            $item->setMeasure($measure);
        }

        $recipeRef = $data['relationships']['recipe']['data']['id'] ?? null;
        if ($recipeRef !== null) {
            $recipe = $this->em->getRepository(\App\Entities\Recipe::class)
                ->findOneBy(['slug' => $recipeRef]);
            $item->setRecipe($recipe);
        }

        $this->em->persist($item);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $item),
            201,
        );
    }
}
