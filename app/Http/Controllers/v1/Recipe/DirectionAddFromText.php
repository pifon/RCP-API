<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Recipe;

use App\Exceptions\v1\NotFoundException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\JsonApi\Document;
use App\Repositories\v1\RecipeRepository;
use App\Services\DirectionParser;
use App\Services\Recipe\DirectionCreationService;
use App\Transformers\v1\DirectionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DirectionAddFromText extends Controller
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly DirectionParser $parser,
        private readonly DirectionCreationService $directionCreation,
        private readonly DirectionTransformer $transformer,
    ) {
    }

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        try {
            $recipe = $this->recipeRepository->getRecipe($slug);
        } catch (\Throwable) {
            throw new NotFoundException("Recipe '{$slug}' not found");
        }

        $data = $request->input('data', []);
        $attrs = $data['attributes'] ?? [];

        $validator = Validator::make($attrs, [
            'direction-text' => ['required', 'string'],
            'use-product-slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'product-ref' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw ValidationErrorException::fromValidationBag($validator->errors());
        }

        $directionText = trim($attrs['direction-text']);
        if ($directionText === '') {
            throw new ValidationErrorException('Direction text cannot be empty.', [
                'direction-text' => ['Provide a non-empty direction sentence.'],
            ]);
        }

        $useProductSlug = isset($attrs['use-product-slug']) && trim((string) $attrs['use-product-slug']) !== ''
            ? trim((string) $attrs['use-product-slug'])
            : null;
        $productRef = isset($attrs['product-ref']) && trim((string) $attrs['product-ref']) !== ''
            ? trim((string) $attrs['product-ref'])
            : null;

        $steps = $this->parser->parse($directionText);

        if ($steps === []) {
            throw new ValidationErrorException(
                'No steps could be parsed from the direction text.',
                ['direction-text' => ['The text did not match any known direction pattern.']],
            );
        }

        $created = [];
        $isFirst = true;
        $replacedOne = false;

        foreach ($steps as $step) {
            $stepAttrs = $this->mapStepToAttributes($step);
            if ($stepAttrs === null) {
                continue;
            }
            if ($useProductSlug !== null && ! empty($stepAttrs['ingredients']) && ! $replacedOne) {
                $refToMatch = $productRef !== null ? strtolower($productRef) : null;
                foreach ($stepAttrs['ingredients'] as $idx => $spec) {
                    $parsedSlug = isset($spec['product-slug']) ? strtolower((string) $spec['product-slug']) : '';
                    $match = $refToMatch !== null ? $parsedSlug === $refToMatch : $idx === 0;
                    if ($match) {
                        $stepAttrs['ingredients'][$idx]['product-slug'] = $useProductSlug;
                        $replacedOne = true;
                        break;
                    }
                }
            }
            if ($isFirst) {
                $stepAttrs['original-text'] = $directionText;
                $isFirst = false;
            }
            // "X into Y" = transfer between intermediates (no new ingredients); store full sentence as note for display
            if (isset($step['into_transfer'])) {
                $stepAttrs['notes'] = array_merge($stepAttrs['notes'] ?? [], [$directionText]);
            }
            // No ingredients (e.g. preheat) → store step's original text as note for full instruction
            $stepSourceText = $step['source_text'] ?? $directionText;
            if (empty($step['ingredients']) && ! isset($step['into_transfer']) && $stepSourceText !== '') {
                $stepAttrs['notes'] = array_merge($stepAttrs['notes'] ?? [], [$stepSourceText]);
            }
            $direction = $this->directionCreation->createDirection($recipe, $stepAttrs, []);
            $created[] = $direction;
        }

        $doc = Document::collection($this->transformer, $created);
        $doc['meta'] = [
            'count' => count($created),
            'prep-time-minutes' => $recipe->getPrepTimeMinutes(),
        ];

        return response()->json($doc, 201);
    }

    /**
     * Map parser step to direction creation attributes.
     * Returns null to skip this step (e.g. unsupported).
     *
     * @param array<string, mixed> $step
     * @return array<string, mixed>|null
     */
    private function mapStepToAttributes(array $step): ?array
    {
        $action = $step['type'] ?? null;
        if ($action === null || $action === '') {
            return null;
        }

        $attrs = ['action' => $action];

        $duration = $step['duration'] ?? null;
        if ($duration !== null && is_array($duration)) {
            $value = (int) ($duration['value'] ?? 0);
            $unit = (string) ($duration['unit'] ?? 'minutes');
            $attrs['duration-minutes'] = $this->durationToMinutes($value, $unit);
        }

        $ingredients = $step['ingredients'] ?? [];
        if (is_array($ingredients) && $ingredients !== []) {
            $ingredientSpecs = [];
            foreach ($ingredients as $ing) {
                if (! is_array($ing)) {
                    continue;
                }
                $name = trim($ing['name'] ?? '');
                if ($name === '') {
                    continue;
                }
                $spec = ['product-slug' => Str::slug($name)];
                $qty = $ing['quantity'] ?? [];
                if (is_array($qty)) {
                    $amount = $qty['amount'] ?? null;
                    $spec['amount'] = $amount !== null ? (float) $amount : 1.0;
                    $unit = $qty['unit'] ?? null;
                    $spec['measure-slug'] = ($unit !== null && $unit !== '') ? $unit : 'pcs';
                } else {
                    $spec['amount'] = 1.0;
                    $spec['measure-slug'] = 'pcs';
                }
                $spec['optional'] = ! empty($ing['optional']);
                $ingredientSpecs[] = $spec;
            }
            if ($ingredientSpecs !== []) {
                $attrs['ingredients'] = $ingredientSpecs;
            }
        }

        return $attrs;
    }

    private function durationToMinutes(int $value, string $unit): int
    {
        return match (strtolower($unit)) {
            'hours', 'hour', 'hr', 'hrs' => $value * 60,
            'seconds', 'second', 'sec', 'secs' => (int) ceil($value / 60),
            default => $value,
        };
    }
}
