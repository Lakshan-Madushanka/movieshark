<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static Builder Filter
 * @method static Builder Sort
 */
class WatchList extends Model
{
    use HasFactory;
    use HasUlids;

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }
}
