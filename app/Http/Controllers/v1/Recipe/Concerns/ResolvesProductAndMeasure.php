<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe\Concerns;

use App\Entities\Measure;
use App\Entities\Product;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ProductNotFoundException;
use Doctrine\ORM\EntityManager;

trait ResolvesProductAndMeasure
{
    /**
     * Resolve product by id (int) or slug (string). Prefer slug when value is non-numeric.
     * If not found by slug, checks product_slug_replacements table, then throws
     * ProductNotFoundException with parsed measure (from context) and suggested similar products.
     *
     * @param array{amount?: float, 'measure-slug'?: string}|null $context   Parsed amount/measure to include in error
     */
    private function resolveProductByIdOrSlug(EntityManager $em, mixed $ref, ?array $context = null): Product
    {
        if ($ref === null || $ref === '') {
            throw new NotFoundException('Product reference is required.');
        }

        $ref = (string) $ref;
        if (is_numeric($ref)) {
            $product = $em->find(Product::class, (int) $ref);
        } else {
            $product = $em->getRepository(Product::class)->findOneBy(['slug' => $ref]);

            if ($product === null) {
                $product = $this->resolveProductFromReplacements($em, $ref);
            }
        }

        if ($product === null) {
            $parsedMeasure = null;
            if ($context !== null && isset($context['amount'], $context['measure-slug'])) {
                $parsedMeasure = [
                    'amount' => (float) $context['amount'],
                    'measure-slug' => (string) $context['measure-slug'],
                ];
            }
            $suggested = $this->findSimilarProducts($em, $ref);
            throw new ProductNotFoundException($ref, 404, $parsedMeasure, $suggested);
        }

        return $product;
    }

    private function resolveProductFromReplacements(EntityManager $em, string $originalSlug): ?Product
    {
        $conn = $em->getConnection();
        try {
            $replacementId = $conn->fetchOne(
                'SELECT replacement_product_id FROM product_slug_replacements WHERE original_slug = ?',
                [$originalSlug],
            );
        } catch (\Throwable) {
            return null;
        }
        if ($replacementId === false || $replacementId === null) {
            return null;
        }
        return $em->find(Product::class, (int) $replacementId);
    }

    /**
     * @return list<array{id: int, name: string, slug: string}>
     */
    private function findSimilarProducts(EntityManager $em, string $ref): array
    {
        $like = '%' . $ref . '%';
        $repo = $em->getRepository(Product::class);
        $products = $repo->createQueryBuilder('p')
            ->where('p.name LIKE :q OR p.slug LIKE :q')
            ->setParameter('q', $like)
            ->orderBy('p.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        $out = [];
        foreach ($products as $p) {
            $out[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'slug' => $p->getSlug(),
            ];
        }
        return $out;
    }

    /**
     * Resolve measure by id (int) or slug (string). Prefer slug when value is non-numeric.
     */
    private function resolveMeasureByIdOrSlug(EntityManager $em, mixed $ref): Measure
    {
        if ($ref === null || $ref === '') {
            throw new NotFoundException('Measure reference is required.');
        }

        $ref = (string) $ref;
        if (is_numeric($ref)) {
            $measure = $em->find(Measure::class, (int) $ref);
        } else {
            $measure = $em->getRepository(Measure::class)->findOneBy(['slug' => $ref]);
        }

        if ($measure === null) {
            throw new NotFoundException("Measure '{$ref}' not found.");
        }

        return $measure;
    }
}
