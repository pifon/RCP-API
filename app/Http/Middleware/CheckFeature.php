<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\Services\FeatureGate;
use Closure;
use Illuminate\Http\Request;

class CheckFeature
{
    public function __construct(
        private readonly FeatureGate $featureGate,
    ) {
    }

    /**
     * Usage in routes: ->middleware('feature:paid_recipes')
     * or ->middleware('feature:max_collections,collections')
     */
    public function handle(Request $request, Closure $next, string $feature, ?string $countTable = null): mixed
    {
        $user = $request->user();
        if ($user === null) {
            return $next($request);
        }

        if ($feature === 'paid_recipes') {
            return $next($request);
        }

        if ($countTable !== null) {
            $limit = $this->featureGate->featureLimit($user, $feature);

            if ($limit !== null) {
                $current = $this->countUserResources($user, $countTable);
                if ($current >= $limit) {
                    return response()->json(
                        Document::errors(new ErrorObject(
                            status: '403',
                            title: 'Plan Limit Reached',
                            detail: 'Your plan allows a maximum of '
                                . "{$limit} {$countTable}. Upgrade to increase your limit.",
                            meta: ['feature' => $feature, 'limit' => $limit, 'current' => $current],
                        )),
                        403,
                        ['Content-Type' => 'application/vnd.api+json'],
                    );
                }
            }
        }

        return $next($request);
    }

    private function countUserResources(\App\Entities\User $user, string $table): int
    {
        $em = app(\Doctrine\ORM\EntityManager::class);
        $conn = $em->getConnection();

        $extra = '';
        if ($table === 'collections') {
            $extra = ' AND deleted_at IS NULL';
        }

        $count = $conn->fetchOne(
            "SELECT COUNT(*) FROM {$table} WHERE user_id = ?{$extra}",
            [$user->getId()],
        );

        return (int) $count;
    }
}
