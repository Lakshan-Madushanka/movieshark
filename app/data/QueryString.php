<?php

declare(strict_types=1);

namespace App\data;

use Illuminate\Support\Arr;

final readonly class QueryString
{
    public function __construct(
        public string $query_term = '',
        public string $genre = '',
        public string $quality = '',
        public string $minimum_rating = '',
        public string $sort_by = '',
        public string $order_by = '',
        public string $page = '',
        public string $limit = '',

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            query_term: Arr::get($data, 'query_term', ''),
            genre: Arr::get($data, 'genre', ''),
            quality: Arr::get($data, 'quality', ''),
            minimum_rating: Arr::get($data, 'minimum_rating', ''),
            sort_by: Arr::get($data, 'sort_by', ''),
            order_by: Arr::get($data, 'order_by', ''),
            page: Arr::get($data, 'page', ''),
            limit: Arr::get($data, 'limit', ''),
        );
    }

    public function getAll(): array
    {
        return array_filter(get_object_vars($this));
    }
}
