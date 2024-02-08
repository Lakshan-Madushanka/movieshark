<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasFilters;
use App\Models\Concerns\HasSorts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder Filter
 * @method static Builder Sort
 */
class WatchList extends Model
{
    use HasFactory;
    use HasUlids;
    use HasFilters;
    use HasSorts;

    private $allowedFilters = [
        'imdb_id' => ['columnName' => 'imdb_id', 'filterType' => 'partial'],
        'yts_id' => ['columnName' => 'yts_id', 'filterType' => 'partial'],
        'genre' => ['columnName' => 'genres', 'columnType' => 'json'],
        'released_date' => ['columnName' => 'released_date', 'filterType' => 'dateBetween'],
        'watched_date' => ['columnName' => 'watched_status', 'filterType' => 'dateBetween'],
        'watched_status' => ['columnName' => 'watched_status', 'filterType' => 'exists'],
        'downloaded_date' => ['columnName' => 'downloaded_status', 'filterType' => 'dateBetween'],
        'downloaded_status' => ['columnName' => 'downloaded_status', 'filterType' => 'exists'],
    ];

    private $allowedSortColumns = [
        'created_at' => [],
        'downloaded_status' => [],
    ];



    protected $table = 'watch_list';

    protected $fillable = [
        'imdb_id',
        'yts_id',
        'image',
        'name',
        'genres',
        'my_rating',
        'released_date',
        'downloaded_status',
        'watched_status',
        'description',
    ];

    protected $casts = [
        'released_date' => 'date:Y',
        'watched_status' => 'date:Y-m-d',
        'downloaded_status' => 'date:Y-m-d',
        'genres' => 'array',
    ];
}
