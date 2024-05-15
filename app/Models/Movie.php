<?php

namespace App\Models;

use App\Models\Concerns\HasFilters;
use App\Models\Concerns\HasSorts;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    use HasUlids;
    use HasFilters;
    use HasSorts;

    public static $allowedFilters = [
        'imdb_id' => ['columnName' => 'imdb_id', 'filterType' => 'partial'],
        'yts_id' => ['columnName' => 'yts_id', 'filterType' => 'partial'],
        'name' => ['columnName' => 'name', 'filterType' => 'partial'],
        'genre' => ['columnName' => 'genres', 'columnType' => 'json'],
        'my_rating' => ['columnName' => 'my_rating', 'filterType' => 'between'],
        'preference' => ['columnName' => 'my_rating', 'filterType' => 'between'],
        'released_date' => ['columnName' => 'released_date', 'filterType' => 'dateBetween'],
        'watched_date' => ['columnName' => 'watched_status', 'filterType' => 'dateBetween'],
        'watched_status' => ['columnName' => 'watched_status', 'filterType' => 'exists'],
        'downloaded_date' => ['columnName' => 'downloaded_status', 'filterType' => 'dateBetween'],
        'downloaded_status' => ['columnName' => 'downloaded_status', 'filterType' => 'exists'],
    ];

    public static $allowedSortColumns = [
        'created_at' => [],
        'downloaded_status' => [],
        'my_rating' => [],
    ];

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
        'released_date' => 'string',
        'watched_status' => 'date:Y-m-d',
        'downloaded_status' => 'date:Y-m-d',
        'genres' => 'array',
    ];
}

