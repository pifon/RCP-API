<?php

declare(strict_types=1);

namespace App\Exceptions\v1;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Exception;
use Illuminate\Http\JsonResponse;

class ProductNotFoundException extends Exception
{
    /**
     * @param array{amount: float, 'measure-slug': string}|null $parsedMeasure   Amount/measure when product not found
     * @param list<array{id: int, name: string, slug: string}>|null $suggestedProducts   Similar products to offer
     */
    public function __construct(
        string $ref,
        int $status = 404,
        private readonly ?array $parsedMeasure = null,
        private readonly ?array $suggestedProducts = null,
    ) {
        parent::__construct("Product '{$ref}' not found.", $status);
    }

    public function render(): JsonResponse
    {
        $meta = [];
        if ($this->parsedMeasure !== null) {
            $meta['parsed-measure'] = $this->parsedMeasure;
        }
        if ($this->suggestedProducts !== null && $this->suggestedProducts !== []) {
            $meta['suggested-products'] = $this->suggestedProducts;
        }

        $links = [
            'products-search' => [
                'href' => '/api/v1/products/search',
                'meta' => [
                    'method' => 'GET',
                    'description' => 'Search products by name or slug (e.g. ?q=olive+oil).',
                ],
            ],
            'create-product' => [
                'href' => '/api/v1/products',
                'meta' => [
                    'method' => 'POST',
                    'description' => 'Create a new product. Required: data.attributes.name. Optional: slug.',
                ],
            ],
        ];
        if ($this->suggestedProducts !== null && $this->suggestedProducts !== []) {
            $links['use-suggested-product'] = [
                'meta' => [
                    'description' => 'Retry with product-slug or product-id set to chosen suggested product.',
                ],
            ];
        }

        $error = new ErrorObject(
            status: '404',
            title: 'Product Not Found',
            detail: $this->getMessage(),
            source: ['parameter' => 'product-slug'],
            meta: $meta !== [] ? $meta : null,
            links: $links,
        );

        return response()->json(
            Document::errors($error),
            404,
            ['Content-Type' => 'application/vnd.api+json'],
        );
    }
}
