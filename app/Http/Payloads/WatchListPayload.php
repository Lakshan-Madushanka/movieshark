<?php

declare(strict_types=1);

namespace App\Http\Payloads;

use Illuminate\Contracts\Support\Arrayable;

final class WatchListPayload implements Arrayable
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $imdb_id = null,
        public readonly ?int $yts_id = null,
        public readonly ?string $image = null,
        public readonly ?array $genres = null,
        public readonly ?string $released_date = null,
        public readonly ?string $downloaded_status = null,
        public readonly ?string $watched_status = null,
        public readonly ?string $description = null,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
