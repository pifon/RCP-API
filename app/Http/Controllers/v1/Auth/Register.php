<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Entities\User;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\UserRepository;
use App\Transformers\v1\UserTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Register extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserTransformer $transformer,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:72'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $existing = $this->userRepository->getUserByUsername($attrs['username']);
        if ($existing !== null) {
            throw new ValidationErrorException(
                'Username already taken.',
                ['/data/attributes/username' => ['This username is already in use.']],
            );
        }

        $user = new User();
        $user->setUsername($attrs['username']);
        $user->setName($attrs['name']);
        $user->setEmail($attrs['email']);
        $user->setPassword($attrs['password']);
        $user->setCreatedAt();
        $user->setUpdatedAt();
        $user->setPasswordChangedAt(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $user),
            201,
        );
    }
}
