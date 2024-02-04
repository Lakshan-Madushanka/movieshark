<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @mixin Model
 * @property array $allowedFilters
 */
trait HasFilters
{
    private  Builder $filterQuery;

    public function scopeFilter(Builder $query): Builder
    {
        $this->filterQuery = $query;

        $this->applyRequestFilters(request());

        return $this->filterQuery;
    }

    private function checkFilterValidity(string $filterName): bool
    {
        if ( ! property_exists($this, 'allowedFilters') || ! is_array($this->allowedFilters)) {
            return false;
        }

        $value = $this->allowedFilters[$filterName]['columnName'] ?? false;

        return (bool) $value;
    }

    public function applyRequestFilters(Request $request): Builder
    {
        if ( ! $filters = $request->collect('filter')->filter()->toArray()) {
            return $this->query();
        }

        collect($filters)->each(function (string|array $value, string $name): void {
            if ( ! $this->checkFilterValidity($name)) {
                return;
            }

            $filterType = $this->allowedFilters[$name]['filterType'] ?? 'exact';
            $columnName = $this->allowedFilters[$name]['columnName'];
            $columnType = $this->allowedFilters[$name]['columnType'] ?? null;

            if ($columnType === 'json') {
                $this->applyJsonFilters($columnName, $value);
                return;
            }

            $this->applyFilter($columnName, $value, $filterType);
        });

        return $this->filterQuery;
    }

    public function applyJsonFilters(string $columnName, $value): void
    {
        $this->filterQuery->whereJsonContains($columnName, $value);
    }

    private function applyFilter(string $columnName, $value, ?string $type = null): void
    {
        if ($type === 'exists') {
            $type = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'notNull' : 'null';
        }

        switch ($type) {
            case 'partial':
                $this->filterQuery->where($columnName, 'like', "%{$value}%");
                break;

            case 'dateBetween':
                $this->filterQuery->whereBetween($columnName, [
                    Arr::get($value, 'from', now()->subCenturies(2)),
                    Arr::get($value, 'to', now()),
                ]);
                break;

            case 'notNull':
                $this->filterQuery->whereNotNull($columnName);
                break;

            case 'null':
                $this->filterQuery->whereNull($columnName);
                break;

            default:
                $this->filterQuery->where($columnName, $value);
        }
    }
}
