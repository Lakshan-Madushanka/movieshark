<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchList extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'watch_list';

    protected $fillable = [
        'imdb_id',
        'yts_id',
        'image',
        'name',
        'genres',
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
