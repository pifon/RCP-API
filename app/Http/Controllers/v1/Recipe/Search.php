<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Search extends Controller
{
    public function __construct(
        private readonly RecipeTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $params = QueryParameters::fromArray($request->query->all());
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(
                Document::collection($this->transformer, [], $params),
            );
        }

        $conn = $this->em->getConnection();
        $like = '%'.$query.'%';

        $countSql = $this->buildSearchSql(true);
        $total = (int) $conn->fetchOne($countSql, $this->buildBindings(
            $like,
            $params,
        ));

        $dataSql = $this->buildSearchSql(false);
        $offset = ($params->pageNumber - 1) * $params->pageSize;
        $dataSql .= ' ORDER BY relevance DESC, r.title ASC';
        $dataSql .= " LIMIT {$params->pageSize} OFFSET {$offset}";

        $rows = $conn->fetchAllAssociative(
            $dataSql,
            $this->buildBindings($like, $params),
        );

        $recipeIds = array_column($rows, 'id');
        $relevanceMap = [];
        foreach ($rows as $row) {
            $relevanceMap[(int) $row['id']] = (int) $row['relevance'];
        }

        $recipes = [];
        if ($recipeIds !== []) {
            $repo = $this->em->getRepository(\App\Entities\Recipe::class);
            $qb = $repo->createQueryBuilder('r')
                ->where('r.id IN (:ids)')
                ->setParameter('ids', $recipeIds);
            $all = $qb->getQuery()->getResult();

            $indexed = [];
            foreach ($all as $recipe) {
                $indexed[$recipe->getId()] = $recipe;
            }
            foreach ($recipeIds as $id) {
                if (isset($indexed[$id])) {
                    $recipes[] = $indexed[$id];
                }
            }
        }

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        $doc = Document::collection(
            $this->transformer,
            $recipes,
            $params,
            $pagination,
        );

        $doc['meta']['search'] = [
            'query' => $query,
            'total-results' => $total,
        ];

        foreach ($doc['data'] as &$item) {
            $recipeId = $this->getRecipeIdBySlug($item['id'], $recipes);
            if (isset($relevanceMap[$recipeId])) {
                $item['meta'] = ['relevance' => $relevanceMap[$recipeId]];
            }
        }
        unset($item);

        return response()->json($doc);
    }

    private function getRecipeIdBySlug(string $slug, array $recipes): int
    {
        foreach ($recipes as $r) {
            if ($r->getSlug() === $slug) {
                return $r->getId();
            }
        }

        return 0;
    }

    private function buildSearchSql(bool $countOnly): string
    {
        $select = $countOnly
            ? 'SELECT COUNT(DISTINCT r.id)'
            : 'SELECT DISTINCT r.id, ('
                .'CASE WHEN r.title LIKE :q THEN 10 ELSE 0 END + '
                .'CASE WHEN r.description LIKE :q THEN 5 ELSE 0 END + '
                .'CASE WHEN p.name LIKE :q THEN 3 ELSE 0 END'
                .') AS relevance';

        return $select.' FROM recipes r'
            .' LEFT JOIN ingredients i ON i.recipe_id = r.id'
            .' LEFT JOIN servings s ON s.id = i.serving_id'
            .' LEFT JOIN products p ON p.id = s.product_id'
            .' WHERE r.deleted_at IS NULL'
            .' AND (r.title LIKE :q OR r.description LIKE :q OR p.name LIKE :q)'
            .$this->buildFilterClauses();
    }

    private function buildFilterClauses(): string
    {
        $sql = '';
        $sql .= ' AND (:status IS NULL OR r.status = :status)';
        $sql .= ' AND (:difficulty IS NULL OR r.difficulty = :difficulty)';
        $sql .= ' AND (:cuisine_id IS NULL OR r.cuisine_id = :cuisine_id)';
        $sql .= ' AND (:dish_type_id IS NULL OR r.dish_type_id = :dish_type_id)';

        return $sql;
    }

    private function buildBindings(string $like, QueryParameters $params): array
    {
        return [
            'q' => $like,
            'status' => $params->hasFilter('status')
                ? $params->getFilter('status') : null,
            'difficulty' => $params->hasFilter('difficulty')
                ? $params->getFilter('difficulty') : null,
            'cuisine_id' => $params->hasFilter('cuisine')
                ? (int) $params->getFilter('cuisine') : null,
            'dish_type_id' => $params->hasFilter('dish-type')
                ? (int) $params->getFilter('dish-type') : null,
        ];
    }
}
