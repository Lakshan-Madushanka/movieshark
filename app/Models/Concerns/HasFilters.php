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
    public static Builder $filterQuery;

    public static function bootHasFilters(): void
    {
        if (property_exists(self::class, 'sortQuery') && isset(self::$sortQuery)) {
            self::$filterQuery = self::$sortQuery;
            return;
        }
        self::$filterQuery = static::query();
    }

    public static function applyFilters(Request $request): Builder
    {
        $model = new static();
        $model->applyRequestFilters($request);

        if (method_exists(self::class, 'applySorts')) {
            $model->applySorts($request);
        }

        return self::$filterQuery;
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

        return self::$filterQuery;
    }

    public function applyJsonFilters(string $columnName, $value): void
    {
        self::$filterQuery->whereJsonContains($columnName, $value);
    }

    private function applyFilter(string $columnName, $value, ?string $type = null): void
    {
        if ($type === 'exists') {
            $type = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'notNull' : 'null';
        }

        switch ($type) {
            case 'partial':
                self::$filterQuery->where($columnName, 'like', "%{$value}%");
                break;

            case 'dateBetween':
                self::$filterQuery->whereBetween($columnName, [
                    Arr::get($value, 'from', now()->subCenturies(2)),
                    Arr::get($value, 'to', now()),
                ]);
                break;

            case 'notNull':
                self::$filterQuery->whereNotNull($columnName);
                break;

            case 'null':
                self::$filterQuery->whereNull($columnName);
                break;

            default:
                self::$filterQuery->where($columnName, $value);
        }
    }
}
