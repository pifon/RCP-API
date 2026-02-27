<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Product;

use App\Entities\Product;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\ProductTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Create extends Controller
{
    public function __construct(
        private readonly ProductTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $name = $attrs['name'];
        $slug = isset($attrs['slug']) && $attrs['slug'] !== ''
            ? $attrs['slug']
            : Str::slug($name);

        $existing = $this->em->getRepository(Product::class)->findOneBy(['slug' => $slug]);
        if ($existing !== null) {
            throw new ValidationErrorException(
                "Product with slug '{$slug}' already exists.",
                ['slug' => ["Slug '{$slug}' is already taken."]],
            );
        }

        $product = new Product();
        $product->setName($name);
        $product->setSlug($slug);
        if (array_key_exists('description', $attrs)) {
            $product->setDescription($attrs['description'] === '' ? null : (string) $attrs['description']);
        }

        $this->setTimestamps($product);
        $this->em->persist($product);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $product),
            201,
        );
    }

    private function setTimestamps(Product $product): void
    {
        $ref = new \ReflectionClass($product);
        $now = new \DateTime();
        foreach (['createdAt', 'updatedAt'] as $prop) {
            $p = $ref->getProperty($prop);
            $p->setValue($product, $now);
        }
    }
}
