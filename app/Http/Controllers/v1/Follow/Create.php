<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Follow;

use App\Entities\Follow;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Create extends Controller
{
    private const ALLOWED_TYPES = ['authors', 'users'];

    public function __construct(
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'followable-type' => ['required', 'string', 'in:' . implode(',', self::ALLOWED_TYPES)],
            'followable-id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $existing = $this->em->getRepository(Follow::class)->findOneBy([
            'follower' => $user,
            'followableType' => $attrs['followable-type'],
            'followableId' => (int) $attrs['followable-id'],
        ]);

        if ($existing !== null) {
            return response()->json(
                Document::meta([
                    'message' => 'Already following.',
                    'follow-id' => $existing->getId(),
                ]),
            );
        }

        $follow = new Follow();
        $follow->setFollower($user);
        $follow->setFollowableType($attrs['followable-type']);
        $follow->setFollowableId((int) $attrs['followable-id']);

        $this->em->persist($follow);
        $this->em->flush();

        return response()->json([
            'jsonapi' => ['version' => '1.1'],
            'data' => [
                'type' => 'follows',
                'id' => (string) $follow->getId(),
                'attributes' => [
                    'followable-type' => $follow->getFollowableType(),
                    'followable-id' => $follow->getFollowableId(),
                    'created-at' => $follow->getCreatedAt()->format('c'),
                ],
            ],
        ], 201);
    }
}
