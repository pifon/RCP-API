<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Plan;

use App\Entities\Plan;
use App\Entities\PlanFeature;
use App\Exceptions\v1\NotFoundException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\PlanTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Show extends Controller
{
    public function __construct(
        private readonly PlanTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $plan = $this->em->getRepository(Plan::class)->findOneBy(['slug' => $slug]);

        if ($plan === null) {
            throw new NotFoundException("Plan '{$slug}' not found.");
        }

        $params = QueryParameters::fromArray($request->query->all());
        $doc = Document::single($this->transformer, $plan, $params);

        $features = $this->em->getRepository(PlanFeature::class)->findBy(['plan' => $plan]);
        $featureMap = [];
        foreach ($features as $f) {
            $featureMap[$f->getFeature()] = $f->getValue();
        }

        $doc['data']['meta'] = ['features' => $featureMap];

        return response()->json($doc);
    }
}
