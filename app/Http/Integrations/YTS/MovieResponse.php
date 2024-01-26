<?php

declare(strict_types=1);

namespace App\Http\Integrations\YTS;

use JsonException;
use Saloon\Http\Response;

class MovieResponse extends Response
{
    /**
     * @return array{
     *      movie_count: int,
     *      limit: int,
     *      page_number: int,
     *      movies: array<int, array{
     *                            id: int,
     *                            title_english: string,
     *                            year: int,
     *                            rating: float,
     *                            language: string,
     *                            genres: array<int, string>,
     *                            medium_cover_image: string
     *                           }>
     *      }|null
     *
     * @throws JsonException
     */
    public function getMovieListData(): ?array
    {
        // @phpstan-ignore-next-line
        return $this->json()['data'];

    }

    /**
     * @return ?array{
     *                movie:array{
     *                            id: int,
     *                            url: string,
     *                            runtime: int,
     *                            description_full: string,
     *                            likes_count: int,
     *                            yts_trailer_code: string,
     *                            large_screenshot_image1: string,
     *                            large_screenshot_image2: string,
     *                            large_screenshot_image3: string,
     *                            title_english: string,
     *                            year: int,
     *                            rating: float,
     *                            language: string,
     *                            genres: array<int, string>,
     *                            medium_cover_image: string,
     *                            mpa_rating: string,
     *                            yt_trailer_code: string,
     *                            like_count: int,
     *                            imdb_code: string,
     *      torrents: array<int, array{
     *                           url: string,
     *                           hash: string,
     *                           quality: string,
     *                           type: string,
     *                           video_codec: string,
     *                           is_repack: string,
     *                           seeds: int,
     *                           peers: int,
     *                           size: string,
     *                           audio_channels: string,
     *              }>
     *          }
     *      }
     *
     * @throws JsonException
     */
    public function getMovieDetails(): ?array
    {
        // @phpstan-ignore-next-line
        return $this->json()['data'];
    }

    /**
     * @return array<int, TorrentData>|null
     *
     * @throws JsonException
     */
    public function buildTorrentData(): ?array
    {
        $data = $this->getMovieDetails();

        $torrentsData = $data['movie']['torrents'] ?? [];

        if ( ! $torrentsData) {
            return null;
        }

        $torrents = [];

        foreach ($torrentsData as $torrent) {
            $torrents[] = new TorrentData(
                url: $torrent['url'],
                hash: $torrent['hash'],
                quality: $torrent['quality'],
                type: $torrent['type'],
                video_codec: $torrent['video_codec'],
                audio_channels: $torrent['audio_channels'],
                is_repack: $torrent['is_repack'],
                seeds: $torrent['seeds'],
                peers: $torrent['peers'],
                size: $torrent['size'],
            );
        }

        return $torrents;
    }
}
