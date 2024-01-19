<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use Saloon\Contracts\DataObjects\WithResponse;
use Saloon\Traits\Responses\HasResponse;

class MovieData implements WithResponse
{
    use HasResponse;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $year,
        public readonly float $rating,
        public readonly string $language,
        /** @var array<int, string> $genres*/
        public readonly array $genres,
        public readonly string $medium_cover_image,
    ) {}

}
