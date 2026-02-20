<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Autocomplete extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $limit = min((int) ($request->query('limit', '10')), 25);

        if (mb_strlen($query) < 2) {
            return response()->json([
                'jsonapi' => ['version' => '1.1'],
                'data' => [],
                'meta' => ['query' => $query],
            ]);
        }

        $conn = $this->em->getConnection();
        $like = $query . '%';

        $rows = $conn->fetchAllAssociative(
            'SELECT DISTINCT r.slug, r.title, r.difficulty, r.status'
            . ' FROM recipes r'
            . ' WHERE r.deleted_at IS NULL'
            . ' AND r.title LIKE :q'
            . ' ORDER BY r.title ASC'
            . ' LIMIT :lim',
            ['q' => $like, 'lim' => $limit],
            ['q' => \Doctrine\DBAL\ParameterType::STRING, 'lim' => \Doctrine\DBAL\ParameterType::INTEGER],
        );

        $suggestions = [];
        foreach ($rows as $row) {
            $suggestions[] = [
                'type' => 'recipes',
                'id' => $row['slug'],
                'attributes' => [
                    'title' => $row['title'],
                    'difficulty' => $row['difficulty'],
                    'status' => $row['status'],
                ],
            ];
        }

        return response()->json([
            'jsonapi' => ['version' => '1.1'],
            'data' => $suggestions,
            'meta' => [
                'query' => $query,
                'count' => count($suggestions),
            ],
        ]);
    }
}
