<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Me;

use App\Entities\Plan;
use App\Entities\UserSubscription;
use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Services\FeatureGate;
use App\Transformers\v1\SubscriptionTransformer;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Subscription extends Controller
{
    public function __construct(
        private readonly SubscriptionTransformer $transformer,
        private readonly FeatureGate $featureGate,
        private readonly EntityManager $em,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $sub = $this->featureGate->activeSubscription($user);

        if ($sub === null) {
            return response()->json(
                Document::meta([
                    'plan' => 'free',
                    'message' => 'No active subscription. Using free plan.',
                ]),
            );
        }

        return response()->json(
            Document::single($this->transformer, $sub),
        );
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $planRef = $data['relationships']['plan']['data']['id']
            ?? ($attrs['plan-slug'] ?? null);

        if ($planRef === null) {
            throw new ValidationErrorException(
                'Plan reference is required.',
                ['plan' => 'Provide relationships.plan.data.id (slug).'],
            );
        }

        $plan = $this->em->getRepository(Plan::class)->findOneBy(['slug' => $planRef]);
        if ($plan === null) {
            throw new NotFoundException("Plan '{$planRef}' not found.");
        }

        $validator = Validator::make($attrs, [
            'billing-cycle' => ['sometimes', 'in:monthly,yearly'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $existing = $this->featureGate->activeSubscription($user);
        if ($existing !== null) {
            $existing->cancel();
        }

        $cycle = $attrs['billing-cycle'] ?? 'monthly';
        $now = new \DateTime();

        $sub = new UserSubscription();
        $sub->setUser($user);
        $sub->setPlan($plan);
        $sub->setBillingCycle($cycle);
        $sub->setCurrentPeriodStart($now);
        $sub->setCurrentPeriodEnd(
            $cycle === 'yearly'
                ? (clone $now)->modify('+1 year')
                : (clone $now)->modify('+1 month')
        );

        $this->em->persist($sub);
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $sub),
            201,
        );
    }

    public function cancel(): JsonResponse
    {
        /** @var \App\Entities\User $user */
        $user = auth()->user();

        $sub = $this->featureGate->activeSubscription($user);
        if ($sub === null) {
            throw new NotFoundException('No active subscription to cancel.');
        }

        $sub->cancel();
        $this->em->flush();

        return response()->json(
            Document::single($this->transformer, $sub),
        );
    }
}
