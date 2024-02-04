<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait HasSorts
{
    private Builder $sortQuery;

    public function scopeSort(Builder $query): Builder
    {
        $this->sortQuery = $query;
        return $this->applyRequestSorts(request());
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

        return $this->sortQuery;
    }

    private function applyDescendingSort($value): void
    {
        $this->sortQuery->orderByRaw("{$value} is null")->orderByDesc($value);
    }

    private function applyAscendingSort($value): void
    {
        $this->sortQuery->orderByRaw("{$value} is null")->orderBy($value);
    }

    private function applyDefaultSort(): Builder
    {
        return $this->sortQuery->latest();
    }
}
