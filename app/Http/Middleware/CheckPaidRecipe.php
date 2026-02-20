<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Entities\Recipe;
use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\Services\FeatureGate;
use Closure;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;

class CheckPaidRecipe
{
    public function __construct(
        private readonly FeatureGate $featureGate,
        private readonly EntityManager $em,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $slug = $request->route('slug');
        if ($slug === null) {
            return $next($request);
        }

        $recipe = $this->em->getRepository(Recipe::class)->findOneBy(['slug' => $slug]);
        if ($recipe === null || $recipe->isFree()) {
            return $next($request);
        }

        $user = $request->user();
        if ($user === null) {
            return $this->denyAccess($recipe);
        }

        if (! $this->featureGate->canAccessPaidRecipes($user)) {
            return $this->denyAccess($recipe);
        }

        return $next($request);
    }

    private function denyAccess(Recipe $recipe): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            Document::errors(new ErrorObject(
                status: '403',
                title: 'Paid Content',
                detail: 'This recipe requires a Pro or Premium subscription.',
                meta: [
                    'recipe' => $recipe->getSlug(),
                    'price' => (float) $recipe->getPrice(),
                    'currency' => $recipe->getCurrency(),
                ],
            )),
            403,
            ['Content-Type' => 'application/vnd.api+json'],
        );
    }
}
