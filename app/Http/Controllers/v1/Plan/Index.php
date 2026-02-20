<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Plan;

use App\Entities\Plan;
use App\Entities\PlanFeature;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\JsonApi\QueryParameters;
use App\Transformers\v1\PlanTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __construct(
        private readonly PlanTransformer $transformer,
        private readonly EntityManager $em,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $params = QueryParameters::fromArray($request->query->all());

        $plans = $this->em->getRepository(Plan::class)->findBy(
            ['isActive' => true],
            ['sortOrder' => 'ASC'],
        );

        $doc = Document::collection($this->transformer, $plans, $params);

        foreach ($doc['data'] as $idx => &$resource) {
            $plan = $plans[$idx];
            $features = $this->em->getRepository(PlanFeature::class)->findBy(['plan' => $plan]);

            $featureMap = [];
            foreach ($features as $f) {
                $featureMap[$f->getFeature()] = $f->getValue();
            }

            $resource['meta'] = ['features' => $featureMap];
        }
        unset($resource);

        return response()->json($doc);
    }
}
