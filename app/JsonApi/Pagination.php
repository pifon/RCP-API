<?php

declare(strict_types=1);

namespace App\JsonApi;

final class Pagination
{
    public function __construct(
        public readonly int $total,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly string $baseUrl,
    ) {
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }

    public function from(): int
    {
        if ($this->total === 0) {
            return 0;
        }

        return (($this->currentPage - 1) * $this->perPage) + 1;
    }

    public function to(): int
    {
        return min($this->currentPage * $this->perPage, $this->total);
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function toMeta(): array
    {
        return [
            'page' => [
                'current-page' => $this->currentPage,
                'per-page' => $this->perPage,
                'from' => $this->from(),
                'to' => $this->to(),
                'total' => $this->total,
                'last-page' => $this->lastPage(),
            ],
        ];
    }

    public function toLinks(): array
    {
        $links = [
            'first' => $this->urlForPage(1),
            'last' => $this->urlForPage($this->lastPage()),
        ];

        if ($this->currentPage > 1) {
            $links['prev'] = $this->urlForPage($this->currentPage - 1);
        }

        if ($this->currentPage < $this->lastPage()) {
            $links['next'] = $this->urlForPage($this->currentPage + 1);
        }

        return $links;
    }

    private function urlForPage(int $page): string
    {
        $separator = str_contains($this->baseUrl, '?') ? '&' : '?';

        return "{$this->baseUrl}{$separator}page[number]={$page}&page[size]={$this->perPage}";
    }
}
