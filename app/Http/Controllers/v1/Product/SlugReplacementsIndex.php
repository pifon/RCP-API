<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Product;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlugReplacementsIndex extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    /**
     * List product slug replacements (optional filter by original_slug).
     * Used to show "what can replace what" and to manage replacements.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $conn = $this->em->getConnection();
        $originalSlug = $request->query('original_slug', '');
        $originalSlug = trim((string) $originalSlug);

        $sql = 'SELECT psr.original_slug, psr.replacement_product_id, '
            . 'p.name AS replacement_name, p.slug AS replacement_slug '
            . 'FROM product_slug_replacements psr '
            . 'JOIN products p ON p.id = psr.replacement_product_id';
        $params = [];
        if ($originalSlug !== '') {
            $sql .= ' WHERE psr.original_slug = ?';
            $params[] = $originalSlug;
        }
        $sql .= ' ORDER BY psr.original_slug ASC';

        try {
            $rows = $conn->fetchAllAssociative($sql, $params);
        } catch (\Throwable) {
            return response()->json(['data' => [], 'meta' => ['total' => 0]], 200);
        }

        $data = array_map(fn (array $row) => [
            'type' => 'product-slug-replacements',
            'attributes' => [
                'original-slug' => $row['original_slug'],
                'replacement-product-id' => (int) $row['replacement_product_id'],
                'replacement-name' => $row['replacement_name'],
                'replacement-slug' => $row['replacement_slug'],
            ],
        ], $rows);

        return response()->json([
            'data' => $data,
            'meta' => ['total' => count($data)],
        ], 200, ['Content-Type' => 'application/vnd.api+json']);
    }
}
