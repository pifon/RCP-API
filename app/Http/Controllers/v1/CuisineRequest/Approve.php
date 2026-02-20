<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\CuisineRequest;

use App\Entities\Cuisine;
use App\Entities\CuisineRequest;
use App\Entities\Recipe;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Transformers\v1\CuisineRequestTransformer;
use DateTime;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Approve extends Controller
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
                'Only pending requests can be approved.',
                ['status' => ['Request has already been '.$cuisineRequest->getStatus().'.']],
            );
        }

        $this->em->getConnection()->beginTransaction();

        try {
            $now = new DateTime;
            $cuisine = new Cuisine;
            $cuisine->setName($cuisineRequest->getName());
            $cuisine->setVariant($cuisineRequest->getVariant());
            $cuisine->setSlug(Str::slug($cuisineRequest->getFullName()));
            $cuisine->setDescription($cuisineRequest->getDescription());
            $this->setTimestamps($cuisine, $now);
            $this->em->persist($cuisine);

            $cuisineRequest->setStatus(CuisineRequest::STATUS_APPROVED);
            $cuisineRequest->setCuisine($cuisine);
            $cuisineRequest->setAdminNotes($request->input('data.attributes.admin-notes'));
            $cuisineRequest->setUpdatedAt(new DateTime);

            $this->em->flush();

            $this->upgradeRecipes($cuisineRequest, $cuisine);

            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return response()->json(
            Document::single($this->transformer, $cuisineRequest),
        );
    }

    private function setTimestamps(Cuisine $cuisine, DateTime $dt): void
    {
        $ref = new \ReflectionClass($cuisine);
        $createdAt = $ref->getProperty('createdAt');
        $createdAt->setValue($cuisine, $dt);
        $updatedAt = $ref->getProperty('updatedAt');
        $updatedAt->setValue($cuisine, $dt);
    }

    /**
     * Migrate recipes that reference this request to the new cuisine.
     */
    private function upgradeRecipes(CuisineRequest $cuisineRequest, Cuisine $cuisine): void
    {
        $recipes = $this->em->getRepository(Recipe::class)->findBy([
            'cuisineRequest' => $cuisineRequest,
        ]);

        foreach ($recipes as $recipe) {
            $recipe->setCuisine($cuisine);
            $recipe->setCuisineRequest(null);
        }
    }
}
