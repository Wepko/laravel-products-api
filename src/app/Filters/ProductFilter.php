<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if (method_exists($this, $filter) && !is_null($value)) {
                $this->$filter($query, $value);
            }
        }

        return $query;
    }

    protected function inStock(Builder $query, bool $value): void
    {
        $query->where('in_stock', $value);
    }

    protected function priceFrom(Builder $query, float $value): void
    {
        $query->where('price', '>=', $value);
    }

    protected function priceTo(Builder $query, float $value): void
    {
        $query->where('price', '<=', $value);
    }

    protected function categoryId(Builder $query, int $value): void
    {
        $query->where('category_id', $value);
    }

    protected function ratingFrom(Builder $query, float $value): void
    {
        $query->where('rating', '>=', $value);
    }

    protected function q(Builder $query, string $value): void
    {
        $query->where('name', 'like', "%{$value}%");
    }
}
