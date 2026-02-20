<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Me;

use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\UserTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Update extends Controller
{
    public function __construct(
        private readonly UserTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8', 'max:72'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        if (isset($attrs['name'])) {
            $user->setName($attrs['name']);
        }

        if (isset($attrs['email'])) {
            $user->setEmail($attrs['email']);
        }

        if (isset($attrs['password'])) {
            $user->setPassword($attrs['password']);
            $user->setPasswordChangedAt(new \DateTime());
        }

        $user->setUpdatedAt();
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $user),
        );
    }
}
