<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Entities\Author;
use App\JsonApi\Document;
use App\JsonApi\ErrorObject;
use App\Repositories\v1\AuthorRepository;
use Closure;
use Illuminate\Http\Request;

class CheckAuthorTier
{
    private const TIER_ORDER = ['free' => 0, 'verified' => 1, 'pro' => 2, 'premium' => 3];

    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    /**
     * Usage: ->middleware('author.tier:pro')
     * Ensures the authenticated user's author profile meets the minimum tier.
     */
    public function handle(Request $request, Closure $next, string $minTier): mixed
    {
        $user = $request->user();
        if ($user === null) {
            return $next($request);
        }

        try {
            $author = $this->authorRepository->getAuthor($user);
        } catch (\Throwable) {
            return response()->json(
                Document::errors(new ErrorObject(
                    status: '403',
                    title: 'Author Required',
                    detail: 'You must have an author profile to perform this action.',
                )),
                403,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        }

        $currentLevel = self::TIER_ORDER[$author->getTier()] ?? 0;
        $requiredLevel = self::TIER_ORDER[$minTier] ?? 0;

        if ($currentLevel < $requiredLevel) {
            return response()->json(
                Document::errors(new ErrorObject(
                    status: '403',
                    title: 'Insufficient Author Tier',
                    detail: "Requires at least '{$minTier}' tier. "
                        . "Your current tier is '{$author->getTier()}'.",
                    meta: ['current-tier' => $author->getTier(), 'required-tier' => $minTier],
                )),
                403,
                ['Content-Type' => 'application/vnd.api+json'],
            );
        }

        return $next($request);
    }
}
