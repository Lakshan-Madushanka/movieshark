<?php

declare(strict_types=1);

namespace App\Http\Integrations\OMDb;

readonly class MovieData
{
    public function __construct(
        public string $plot,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
