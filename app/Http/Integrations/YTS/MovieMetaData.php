<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use Saloon\Contracts\DataObjects\WithResponse;
use Saloon\Traits\Responses\HasResponse;

class MovieMetaData implements WithResponse
{
    use HasResponse;

    public function __construct(
        public readonly int $movie_count,
        public readonly int $limit,
        public readonly int $page,
    ) {}
}
