<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait HasSorts
{
    public static Builder $sortQuery;

    public static function bootHasSorts(): void
    {
        if (property_exists(self::class, 'filterQuery') && isset(self::$filterQuery)) {
            self::$sortQuery = self::$filterQuery;
            return;
        }
        self::$sortQuery = static::query();
    }

    public static function applySorts(Request $request): Builder
    {
        return (new static())->applyRequestSorts($request);
    }

    public function checkSortColumnValidity($column): bool
    {
        if (property_exists($this, 'allowedSortColumns') || is_array($this->allowedSortColumns)) {
            return false;
        }

        return in_array($column, $this->allowedSortColumns);
    }

    private function applyRequestSorts(Request $request): Builder
    {
        if ( ! $request->filled('sort')) {
            return $this->applyDefaultSort();
        }

        $sorts = $request->query('sort');

        if (str($sorts)->contains(',')) {
            $sorts = collect(explode(',', $sorts));
        }

        $sorts = collect(Arr::wrap($sorts));

        $sorts->each(function ($value): void {
            if ($this->checkSortColumnValidity($value)) {
                return;
            }

            $columnName = $this->allowedSortColumns[$value]['columnName'] ?? $value;

            if (str($value)->startsWith('-')) {
                $columnName = str($columnName)->replaceFirst('-', '')->toString();
                $this->applyDescendingSort($columnName);
                return;
            }

            $this->applyAscendingSort($columnName);
        });

        return self::$sortQuery;
    }

    private function applyDescendingSort($value): void
    {
        self::$sortQuery->orderByRaw("{$value} is null")->orderByDesc($value);
    }

    private function applyAscendingSort($value): void
    {
        self::$sortQuery->orderByRaw("{$value} is null")->orderBy($value);
    }

    private function applyDefaultSort(): Builder
    {
        return self::$sortQuery->latest();
    }
}
