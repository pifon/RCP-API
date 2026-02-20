<?php

declare(strict_types=1);

namespace App\JsonApi;

abstract class AbstractTransformer
{
    abstract public function getType(): string;

    abstract public function getId(object $entity): string;

    abstract public function selfLink(object $entity): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function attributes(object $entity): array;

    /**
     * Define available relationships.
     *
     * Return format:
     *   [
     *     'author' => [
     *       'data'        => ['type' => 'authors', 'id' => 'john-doe'],
     *       'links'       => ['self' => '...', 'related' => '...'],
     *       'entity'      => $authorEntity,           // for includes
     *       'transformer' => AuthorTransformer::class, // for includes
     *     ],
     *   ]
     *
     * @return array<string, array<string, mixed>>
     */
    protected function relationships(object $entity): array
    {
        return [];
    }

    /**
     * @return array<string, mixed> A full JSON:API resource object
     */
    public function toResource(object $entity, ?QueryParameters $params = null): array
    {
        $type = $this->getType();
        $attrs = $this->attributes($entity);

        if ($params !== null) {
            $requestedFields = $params->getFieldsFor($type);
            if ($requestedFields !== null) {
                $attrs = array_intersect_key($attrs, array_flip($requestedFields));
            }
        }

        $resource = [
            'type' => $type,
            'id' => $this->getId($entity),
            'attributes' => $attrs,
        ];

        $rels = $this->relationships($entity);
        if ($rels !== []) {
            $resource['relationships'] = $this->buildRelationshipLinkage($rels);
        }

        $resource['links'] = ['self' => $this->selfLink($entity)];

        return $resource;
    }

    /**
     * Collect full resource objects for requested includes.
     *
     * @return array<int, array<string, mixed>>
     */
    public function collectIncludes(object $entity, QueryParameters $params): array
    {
        $included = [];
        $rels = $this->relationships($entity);

        foreach ($params->include as $name) {
            if (! isset($rels[$name]['entity'], $rels[$name]['transformer'])) {
                continue;
            }

            $related = $rels[$name]['entity'];

            /** @var AbstractTransformer $transformer */
            $transformer = new ($rels[$name]['transformer'])();

            if (is_iterable($related)) {
                foreach ($related as $item) {
                    $included[] = $transformer->toResource($item, $params);
                }
            } else {
                $included[] = $transformer->toResource($related, $params);
            }
        }

        return $included;
    }

    /**
     * Strip internal keys (entity, transformer) and keep only data + links for the response.
     *
     * @return array<string, array<string, mixed>>
     */
    private function buildRelationshipLinkage(array $rels): array
    {
        $output = [];
        foreach ($rels as $name => $rel) {
            $entry = [];

            if (isset($rel['data'])) {
                $entry['data'] = $rel['data'];
            }

            if (isset($rel['links'])) {
                $entry['links'] = $rel['links'];
            }

            $output[$name] = $entry;
        }

        return $output;
    }
}
