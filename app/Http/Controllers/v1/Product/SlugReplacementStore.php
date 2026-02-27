<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Product;

use App\Entities\Product;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SlugReplacementStore extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    /**
     * Create a slug replacement: when "original_slug" is requested and not found, use this product instead.
     * Use case: misspelling or alternate name (e.g. "flour" -> all-purpose flour product).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'original-slug' => ['required', 'string', 'max:255'],
            'replacement-product-id' => ['required_without:replacement-slug', 'nullable', 'integer', 'min:1'],
            'replacement-slug' => ['required_without:replacement-product-id', 'nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $originalSlug = trim($attrs['original-slug']);
        if ($originalSlug === '') {
            throw new ValidationErrorException('original-slug cannot be empty.', ['original-slug' => ['Required.']]);
        }

        $replacementProductId = isset($attrs['replacement-product-id'])
            ? (int) $attrs['replacement-product-id'] : null;
        if ($replacementProductId === null && ! empty($attrs['replacement-slug'])) {
            $slug = trim($attrs['replacement-slug']);
            $product = $this->em->getRepository(Product::class)->findOneBy(['slug' => $slug]);
            if ($product === null) {
                throw new NotFoundException("Replacement product slug '{$attrs['replacement-slug']}' not found.");
            }
            $replacementProductId = $product->getId();
        }
        if ($replacementProductId === null) {
            throw new ValidationErrorException(
                'Provide replacement-product-id or replacement-slug.',
                ['replacement' => ['Required.']],
            );
        }

        $product = $this->em->find(Product::class, $replacementProductId);
        if ($product === null) {
            throw new NotFoundException("Replacement product id {$replacementProductId} not found.");
        }

        $conn = $this->em->getConnection();
        $now = date('Y-m-d H:i:s');
        try {
            $conn->insert('product_slug_replacements', [
                'original_slug' => $originalSlug,
                'replacement_product_id' => $replacementProductId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'unique')) {
                $conn->update(
                    'product_slug_replacements',
                    ['replacement_product_id' => $replacementProductId, 'updated_at' => $now],
                    ['original_slug' => $originalSlug],
                );
            } else {
                throw $e;
            }
        }

        return response()->json([
            'data' => [
                'type' => 'product-slug-replacements',
                'attributes' => [
                    'original-slug' => $originalSlug,
                    'replacement-product-id' => $product->getId(),
                    'replacement-name' => $product->getName(),
                    'replacement-slug' => $product->getSlug(),
                ],
            ],
        ], 201, ['Content-Type' => 'application/vnd.api+json']);
    }
}
