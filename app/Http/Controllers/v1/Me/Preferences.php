<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Me;

use App\Entities\UserPreference;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Preferences extends Controller
{
    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $pref = $this->getOrCreate($user);

        $cuisineIds = $this->getPivotIds('user_preferred_cuisines', 'cuisine_id', $user->getId());
        $excludedIds = $this->getPivotIds('user_excluded_products', 'product_id', $user->getId());
        $allergenIds = $this->getPivotIds('user_dietary_restrictions', 'allergen_id', $user->getId());

        return response()->json([
            'jsonapi' => ['version' => '1.1'],
            'data' => [
                'type' => 'user-preferences',
                'id' => (string) $user->getId(),
                'attributes' => [
                    'spice-tolerance' => $pref->getSpiceTolerance(),
                    'preferred-cuisine-ids' => $cuisineIds,
                    'excluded-product-ids' => $excludedIds,
                    'dietary-restriction-allergen-ids' => $allergenIds,
                ],
                'links' => [
                    'self' => '/api/v1/me/preferences',
                ],
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();
        $pref = $this->getOrCreate($user);

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        if (isset($attrs['spice-tolerance'])) {
            $pref->setSpiceTolerance((int) $attrs['spice-tolerance']);
        }

        $conn = $this->em->getConnection();

        if (array_key_exists('preferred-cuisine-ids', $attrs)) {
            $conn->executeStatement(
                'DELETE FROM user_preferred_cuisines WHERE user_id = ?',
                [$user->getId()],
            );
            foreach ((array) $attrs['preferred-cuisine-ids'] as $cid) {
                $conn->executeStatement(
                    'INSERT INTO user_preferred_cuisines (user_id, cuisine_id) VALUES (?, ?)',
                    [$user->getId(), (int) $cid],
                );
            }
        }

        if (array_key_exists('excluded-product-ids', $attrs)) {
            $conn->executeStatement(
                'DELETE FROM user_excluded_products WHERE user_id = ?',
                [$user->getId()],
            );
            foreach ((array) $attrs['excluded-product-ids'] as $pid) {
                $conn->executeStatement(
                    'INSERT INTO user_excluded_products (user_id, product_id) VALUES (?, ?)',
                    [$user->getId(), (int) $pid],
                );
            }
        }

        if (array_key_exists('dietary-restriction-allergen-ids', $attrs)) {
            $conn->executeStatement(
                'DELETE FROM user_dietary_restrictions WHERE user_id = ?',
                [$user->getId()],
            );
            foreach ((array) $attrs['dietary-restriction-allergen-ids'] as $aid) {
                $conn->executeStatement(
                    'INSERT INTO user_dietary_restrictions (user_id, allergen_id) VALUES (?, ?)',
                    [$user->getId(), (int) $aid],
                );
            }
        }

        $this->em->flush();

        return $this->show($request);
    }

    private function getOrCreate(\App\Entities\User $user): UserPreference
    {
        $pref = $this->em->getRepository(UserPreference::class)->findOneBy(['user' => $user]);

        if ($pref === null) {
            $pref = new UserPreference();
            $pref->setUser($user);
            $this->em->persist($pref);
            $this->em->flush();
        }

        return $pref;
    }

    /**
     * @return int[]
     */
    private function getPivotIds(string $table, string $column, int $userId): array
    {
        $rows = $this->em->getConnection()->fetchAllAssociative(
            "SELECT {$column} FROM {$table} WHERE user_id = ?",
            [$userId],
        );

        return array_map(fn (array $row) => (int) $row[$column], $rows);
    }
}
