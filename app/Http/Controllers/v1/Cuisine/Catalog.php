<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Cuisine;

use App\Exceptions\v1\BadRequestException;
use App\Exceptions\v1\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Repositories\v1\CuisineRepository;
use App\Rules\LowercaseAlphaRule;
use App\Transformers\v1\CuisineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Catalog extends Controller
{
    public function __construct(
        private readonly CuisineRepository $repository,
        private readonly CuisineTransformer $transformer
    ) {}

    /**
     * @throws BadRequestException
     * @throws ValidationErrorException
     * @throws ValidationException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'filter' => ['sometimes', 'string', new LowercaseAlphaRule],
            'limit' => 'sometimes|integer|min:1|max:50',
        ], [
            'filter.string' => trans('cuisine.filter.string'),
            'limit.integer' => trans('cuisine.limit.integer'),
            'limit.min' => trans('cuisine.limit.min'),
            'limit.max' => trans('cuisine.limit.max'),
        ]);

        if (! empty(array_diff(array_keys($request->all()), ['filter', 'limit']))) {
            throw new BadRequestException(
                trans('cuisine.unexpected_fields.message'),
                [
                    trans('cuisine.unexpected_fields.error'),
                ]
            );
        }

        if ($validator->fails()) {
            throw new ValidationErrorException(
                'Validation Error',
                $validator->errors()->toArray()
            );
        }

        $params = collect($validator->validated());

        $params->transform(function (mixed $value, string $key): mixed {
            if ($key === 'filter' && is_string($value)) {
                return ucfirst(strtolower($value));
            }
            if ($key === 'limit' && is_numeric($value)) {
                return intval($value);
            }

            return $value;
        });

        $filter = $params['filter'] ?? null;
        $limit = $params['limit'] ?? null;

        $cuisines = $this->repository->getCuisines($filter, $limit);

        return response()->json($this->transformer->transformSet($cuisines));
    }
}
