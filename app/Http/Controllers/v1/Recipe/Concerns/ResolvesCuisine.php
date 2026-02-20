<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe\Concerns;

use App\Entities\Cuisine;
use App\Entities\CuisineRequest;
use App\Entities\Recipe;
use App\Exceptions\v1\NotFoundException;
use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;

trait ResolvesCuisine
{
    /**
     * Resolve cuisine or cuisine-request from relationships and apply to recipe.
     * Supports cuisine by ID or by name/slug.
     * Returns a JsonResponse with a 422 error (including fuzzy suggestions)
     * if cuisine is missing or invalid, or null on success.
     *
     * @param array<string, mixed> $relationships
     */
    private function applyCuisine(
        array $relationships,
        Recipe $recipe,
        EntityManager $em,
    ): ?JsonResponse {
        $cuisineData = $relationships['cuisine']['data'] ?? null;
        $requestData = $relationships['cuisine-request']['data'] ?? null;

        if ($cuisineData !== null) {
            $cuisine = $this->findCuisine($cuisineData, $em);
            if ($cuisine === null) {
                $query = $cuisineData['name'] ?? $cuisineData['slug'] ?? null;
                return $this->cuisineNotFoundResponse($cuisineData, $query, $em);
            }
            $recipe->setCuisine($cuisine);
            $recipe->setCuisineRequest(null);
            return null;
        }

        if ($requestData !== null) {
            $cuisineRequest = $em->find(CuisineRequest::class, (int) $requestData['id']);
            if ($cuisineRequest === null) {
                throw new NotFoundException("Cuisine request #{$requestData['id']} not found.");
            }
            $recipe->setCuisineRequest($cuisineRequest);
            return null;
        }

        if ($recipe->getCuisine() === null && $recipe->getCuisineRequest() === null) {
            return $this->cuisineMissingResponse();
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function findCuisine(array $data, EntityManager $em): ?Cuisine
    {
        if (isset($data['id'])) {
            return $em->find(Cuisine::class, (int) $data['id']);
        }

        $repo = $em->getRepository(Cuisine::class);

        if (isset($data['slug'])) {
            return $repo->findOneBy(['slug' => $data['slug']]);
        }

        if (isset($data['name'])) {
            $name = $data['name'];
            $variant = $data['variant'] ?? null;

            $criteria = ['name' => $name];
            if ($variant !== null) {
                $criteria['variant'] = $variant;
            }

            $found = $repo->findOneBy($criteria);
            if ($found !== null) {
                return $found;
            }

            return $repo->findOneBy(['slug' => \Illuminate\Support\Str::slug($name)]);
        }

        return null;
    }

    /**
     * @return list<array{id: int, name: string, slug: string, variant: string|null}>
     */
    private function findSimilarCuisines(?string $query, EntityManager $em): array
    {
        if ($query === null || strlen($query) < 2) {
            return [];
        }

        $conn = $em->getConnection();
        $likeTerm = '%' . $query . '%';

        /** @var list<array{id: int, name: string, slug: string, variant: string|null}> $rows */
        $rows = $conn->fetchAllAssociative(
            'SELECT id, name, slug, variant
             FROM cuisines
             WHERE name LIKE :q OR variant LIKE :q OR slug LIKE :q
             ORDER BY name, variant
             LIMIT 10',
            ['q' => $likeTerm],
        );

        if ($rows !== []) {
            return $rows;
        }

        return $this->levenshteinSearch($query, $em);
    }

    /**
     * @return list<array{id: int, name: string, slug: string, variant: string|null}>
     */
    private function levenshteinSearch(string $query, EntityManager $em): array
    {
        $conn = $em->getConnection();

        /** @var list<array{id: int, name: string, slug: string, variant: string|null}> $all */
        $all = $conn->fetchAllAssociative(
            'SELECT id, name, slug, variant FROM cuisines ORDER BY name, variant',
        );

        $queryLower = strtolower($query);
        $scored = [];

        foreach ($all as $row) {
            $targets = [strtolower($row['name']), strtolower($row['slug'])];
            if ($row['variant'] !== null) {
                $targets[] = strtolower($row['variant']);
                $targets[] = strtolower($row['name'] . ' ' . $row['variant']);
            }

            $best = PHP_INT_MAX;
            foreach ($targets as $t) {
                $dist = levenshtein($queryLower, $t);
                $best = min($best, $dist);
            }

            $threshold = max(3, (int) ceil(strlen($query) * 0.5));
            if ($best <= $threshold) {
                $scored[] = ['row' => $row, 'distance' => $best];
            }
        }

        usort($scored, fn ($a, $b) => $a['distance'] <=> $b['distance']);

        return array_map(
            fn ($item) => $item['row'],
            array_slice($scored, 0, 5),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function cuisineNotFoundResponse(
        array $data,
        ?string $query,
        EntityManager $em,
    ): JsonResponse {
        $label = $query ?? (string) ($data['id'] ?? 'unknown');
        $suggestions = $this->findSimilarCuisines($query, $em);

        $suggestionLinks = [];
        foreach ($suggestions as $s) {
            $display = $s['variant'] !== null
                ? $s['name'] . ' - ' . $s['variant']
                : $s['name'];

            $suggestionLinks["cuisine:{$s['slug']}"] = [
                'href' => '/api/v1/cuisines/' . $s['slug'],
                'meta' => [
                    'id' => $s['id'],
                    'name' => $display,
                    'slug' => $s['slug'],
                ],
            ];
        }

        $detail = "Cuisine '{$label}' not found.";
        if ($suggestions !== []) {
            $names = array_map(
                fn ($s) => $s['variant'] !== null
                    ? $s['name'] . ' - ' . $s['variant']
                    : $s['name'],
                $suggestions,
            );
            $detail .= ' Did you mean: ' . implode(', ', $names) . '?';
        }

        $links = $suggestionLinks;
        $links['create-cuisine-request'] = [
            'href' => '/api/v1/cuisine-requests',
            'meta' => [
                'method' => 'POST',
                'description' => 'Submit a request to create a new cuisine.',
            ],
        ];

        $error = new ErrorObject(
            status: '422',
            title: 'Validation Error',
            detail: $detail,
            source: ['pointer' => '/data/relationships/cuisine'],
            links: $links,
        );

        return response()->json(Document::errors($error), 422);
    }

    private function cuisineMissingResponse(): JsonResponse
    {
        $error = new ErrorObject(
            status: '422',
            title: 'Validation Error',
            detail: 'Cuisine is required. Provide an existing cuisine or a cuisine-request.',
            source: ['pointer' => '/data/relationships/cuisine'],
            links: [
                'cuisines' => [
                    'href' => '/api/v1/cuisines',
                    'meta' => ['description' => 'List available cuisines.'],
                ],
                'create-cuisine-request' => [
                    'href' => '/api/v1/cuisine-requests',
                    'meta' => [
                        'method' => 'POST',
                        'description' => 'Submit a request to create a new cuisine.',
                    ],
                ],
            ],
        );

        return response()->json(Document::errors($error), 422);
    }
}
