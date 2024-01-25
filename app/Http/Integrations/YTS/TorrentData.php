<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use Saloon\Contracts\DataObjects\WithResponse;
use Saloon\Traits\Responses\HasResponse;

class TorrentData implements WithResponse
{
    use HasResponse;

    public function __construct(
        public readonly string $url,
        public readonly string $hash,
        public readonly string $quality,
        public readonly string $type,
        public readonly string $video_codec,
        public readonly string $audio_channels,
        public readonly string $is_repack,
        public readonly int $seeds,
        public readonly int $peers,
        public readonly string $size,
    ) {}
}
