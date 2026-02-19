<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Pantry;

use App\Entities\PantryItem;
use App\Entities\Recipe;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\Pagination;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\RecipeTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookableRecipes extends Controller
{
    public function __construct(
        private readonly RecipeTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $params = QueryParameters::fromArray($request->query->all());

        $maxMissing = (int) ($params->getFilter('max-missing') ?? 0);

        $pantryProductIds = $this->getPantryProductIds($user);

        if (empty($pantryProductIds)) {
            return response()->json(
                Document::collection($this->transformer, [], $params, new Pagination(
                    total: 0,
                    currentPage: 1,
                    perPage: $params->pageSize,
                    baseUrl: $request->url(),
                )),
            );
        }

        $conn = $this->em->getConnection();

        /*
         * For each published recipe, count total distinct ingredient products
         * and how many of those the user has in the pantry.
         * Recipes where (total - matched) <= maxMissing are cookable.
         *
         * Chain: recipes -> ingredients -> servings -> products
         */
        $sql = <<<'SQL'
            SELECT
                r.id AS recipe_id,
                COUNT(DISTINCT s.product_id) AS total_ingredients,
                COUNT(DISTINCT CASE WHEN s.product_id IN (%s) THEN s.product_id END) AS matched
            FROM recipes r
            INNER JOIN ingredients i ON i.recipe_id = r.id
            INNER JOIN servings s ON s.id = i.serving_id
            WHERE r.deleted_at IS NULL
              AND r.status = 'published'
            GROUP BY r.id
            HAVING (COUNT(DISTINCT s.product_id)
                - COUNT(DISTINCT CASE WHEN s.product_id IN (%s) THEN s.product_id END)) <= ?
            ORDER BY matched DESC, total_ingredients ASC
        SQL;

        $placeholders = implode(',', array_fill(0, count($pantryProductIds), '?'));
        $sql = sprintf($sql, $placeholders, $placeholders);

        $bindValues = array_merge($pantryProductIds, $pantryProductIds, [$maxMissing]);

        $rows = $conn->fetchAllAssociative(
            $sql,
            array_values($bindValues),
        );

        $total = count($rows);

        $offset = ($params->pageNumber - 1) * $params->pageSize;
        $pageRows = array_slice($rows, $offset, $params->pageSize);

        $recipeIds = array_column($pageRows, 'recipe_id');
        $recipes = [];

        if (!empty($recipeIds)) {
            $qb = $this->em->createQueryBuilder()
                ->select('r')
                ->from(Recipe::class, 'r')
                ->where('r.id IN (:ids)')
                ->setParameter('ids', $recipeIds);

            $found = $qb->getQuery()->getResult();

            $indexed = [];
            foreach ($found as $recipe) {
                $indexed[$recipe->getId()] = $recipe;
            }

            foreach ($recipeIds as $rid) {
                if (isset($indexed[$rid])) {
                    $recipes[] = $indexed[$rid];
                }
            }
        }

        $pagination = new Pagination(
            total: $total,
            currentPage: $params->pageNumber,
            perPage: $params->pageSize,
            baseUrl: $request->url(),
        );

        $doc = Document::collection($this->transformer, $recipes, $params, $pagination);

        $matchMeta = [];
        foreach ($pageRows as $row) {
            $matchMeta[(int) $row['recipe_id']] = [
                'total-ingredients' => (int) $row['total_ingredients'],
                'matched' => (int) $row['matched'],
                'missing' => (int) $row['total_ingredients'] - (int) $row['matched'],
            ];
        }

        foreach ($doc['data'] as $idx => &$resource) {
            $recipeId = $recipeIds[$idx] ?? null;
            if ($recipeId !== null && isset($matchMeta[$recipeId])) {
                $resource['meta'] = $matchMeta[$recipeId];
            }
        }
        unset($resource);

        return response()->json($doc);
    }

    /**
     * @return int[]
     */
    private function getPantryProductIds(\App\Entities\User $user): array
    {
        $items = $this->em->getRepository(PantryItem::class)->findBy(['user' => $user]);

        $ids = [];
        foreach ($items as $item) {
            if ((float) $item->getQuantity() > 0) {
                $ids[] = $item->getProduct()->getId();
            }
        }

        return array_unique($ids);
    }
}
