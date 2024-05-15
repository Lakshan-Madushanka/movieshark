<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use Illuminate\Contracts\Support\Arrayable;
use Saloon\Contracts\DataObjects\WithResponse;
use Saloon\Traits\Responses\HasResponse;

class MovieData implements Arrayable, WithResponse
{
    use HasResponse;

    public readonly string|int $year;
    public readonly float|string $rating;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        int $year,
        float $rating,
        public readonly string $language,
        /** @var array<int, string> $genres */
        public readonly array $genres,
        public readonly string $cover_image,
        public readonly ?string $mpa_rating = null,
        public readonly ?string $imdb_code = null,
        public readonly ?int $runtime = null,
        public readonly ?string $description_full = null,
        public readonly ?int $like_count = null,
        public readonly ?string $yt_trailer_code = null,
        public readonly ?string $image1 = null,
        public readonly ?string $image2 = null,
        public readonly ?string $image3 = null,
        /** @var array<int, TorrentData>|null $torrents */
        public readonly ?array $torrents = null,
    ) {
        $this->year = $year === 0 ? 'N/A' : $year;
        $this->rating =  number_format($rating, 1);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
