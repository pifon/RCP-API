<?php

declare(strict_types=1);

namespace App\JsonApi;

final class Document
{
    private const VERSION = '1.1';

    /**
     * Build a JSON:API document for a single resource.
     *
     * @return array<string, mixed>
     */
    public static function single(
        AbstractTransformer $transformer,
        object $entity,
        ?QueryParameters $params = null,
    ): array {
        $params ??= new QueryParameters();

        $doc = [
            'jsonapi' => ['version' => self::VERSION],
            'data' => $transformer->toResource($entity, $params),
        ];

        $included = $transformer->collectIncludes($entity, $params);
        if ($included !== []) {
            $doc['included'] = self::deduplicateIncluded($included);
        }

        return $doc;
    }

    /**
     * Build a JSON:API document for a resource collection.
     *
     * @param object[] $entities
     * @return array<string, mixed>
     */
    public static function collection(
        AbstractTransformer $transformer,
        array $entities,
        ?QueryParameters $params = null,
        ?Pagination $pagination = null,
    ): array {
        $params ??= new QueryParameters();

        $data = array_map(
            fn (object $e) => $transformer->toResource($e, $params),
            $entities,
        );

        $doc = [
            'jsonapi' => ['version' => self::VERSION],
            'data' => array_values($data),
        ];

        $included = [];
        foreach ($entities as $entity) {
            $included = array_merge($included, $transformer->collectIncludes($entity, $params));
        }

        if ($included !== []) {
            $doc['included'] = self::deduplicateIncluded($included);
        }

        if ($pagination !== null) {
            $doc['meta'] = $pagination->toMeta();
            $doc['links'] = $pagination->toLinks();
        }

        return $doc;
    }

    /**
     * Build a JSON:API error document.
     *
     * @return array<string, mixed>
     */
    public static function errors(ErrorObject ...$errors): array
    {
        return [
            'jsonapi' => ['version' => self::VERSION],
            'errors' => array_map(fn (ErrorObject $e) => $e->toArray(), $errors),
        ];
    }

    /**
     * Build a JSON:API meta-only document (e.g. for successful deletes).
     *
     * @return array<string, mixed>
     */
    public static function meta(array $meta): array
    {
        return [
            'jsonapi' => ['version' => self::VERSION],
            'meta' => $meta,
        ];
    }

    /**
     * Remove duplicate included resources (same type + id).
     *
     * @param array<int, array<string, mixed>> $included
     * @return array<int, array<string, mixed>>
     */
    private static function deduplicateIncluded(array $included): array
    {
        $seen = [];
        $unique = [];

        foreach ($included as $resource) {
            $key = $resource['type'] . ':' . $resource['id'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $resource;
        }

        return $unique;
    }
}
