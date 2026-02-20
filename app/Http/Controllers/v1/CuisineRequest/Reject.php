<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\CuisineRequest;

use App\Entities\CuisineRequest;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\CuisineRequestTransformer;
use DateTime;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Reject extends Controller
{
    public function __construct(
        private readonly CuisineRequestTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, int $id): JsonResponse
    {
        $cuisineRequest = $this->em->find(CuisineRequest::class, $id);

        if ($cuisineRequest === null) {
            throw new NotFoundException("Cuisine request #{$id} not found.");
        }

        if (! $cuisineRequest->isPending()) {
            throw new ValidationErrorException(
                'Only pending requests can be rejected.',
                ['status' => ['Request has already been '.$cuisineRequest->getStatus().'.']],
            );
        }

        $cuisineRequest->setStatus(CuisineRequest::STATUS_REJECTED);
        $cuisineRequest->setAdminNotes($request->input('data.attributes.admin-notes'));
        $cuisineRequest->setUpdatedAt(new DateTime);

        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $cuisineRequest),
        );
    }
}
