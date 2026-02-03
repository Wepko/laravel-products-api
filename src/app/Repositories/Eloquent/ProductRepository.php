<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Product\ProductFilterDTO;
use App\Enums\ProductSortEnum;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(ProductFilterDTO $productFilterDTO): LengthAwarePaginator
    {
        $query = $this->buildQuery($productFilterDTO);

        return $query->paginate(
            $productFilterDTO->perPage,
            ['*'],
            'page',
            $productFilterDTO->page
        );
    }

    public function simplePaginate(ProductFilterDTO $productFilterDTO): Paginator
    {
        $query = $this->buildQuery($productFilterDTO);

        return $query->simplePaginate(
            $productFilterDTO->perPage,
            ['*'],
            'page',
            $productFilterDTO->page
        );
    }

    public function cursorPaginate(ProductFilterDTO $productFilterDTO): CursorPaginator
    {
        $query = $this->buildQuery($productFilterDTO);

        if (!$productFilterDTO->sort) {
            $productFilterDTO->sort = ProductSortEnum::CREATED_AT_DESC->value; // или 'id_desc'
        }

        return $query->cursorPaginate(
            $productFilterDTO->perPage,
            ['*'],
            'cursor',
            $productFilterDTO->cursor // Параметр cursor из DTO
        );
    }


    /**
     * Общий метод для построения запроса с фильтрами и сортировкой
     */
    private function buildQuery(ProductFilterDTO $filters)
    {
        $query = Product::query()->with('category');

        // Фильтр по наличию
        if ($filters->inStock !== null) {
            $query->where('in_stock', $filters->inStock);
        }

        // Фильтр по цене от
        if ($filters->priceFrom !== null) {
            $query->where('price', '>=', $filters->priceFrom);
        }

        // Фильтр по цене до
        if ($filters->priceTo !== null) {
            $query->where('price', '<=', $filters->priceTo);
        }

        // Фильтр по категории
        if ($filters->categoryId !== null) {
            $query->where('category_id', $filters->categoryId);
        }

        // Фильтр по рейтингу от
        if ($filters->ratingFrom !== null) {
            $query->where('rating', '>=', $filters->ratingFrom);
        }

        // Поиск по названию
        if ($filters->q) {
            $query->where('name', 'like', "%{$filters->q}%");
        }

        // Сортировка
        $this->applySorting($query, $filters->sort);

        return $query;
    }

    /**
     * Применение сортировки
     */
    private function applySorting($query, ?string $sort): void
    {
        $sorting = match ($sort) {
            ProductSortEnum::PRICE_DESC->value => ['price', 'desc'],
            ProductSortEnum::PRICE_ASC->value => ['price', 'asc'],
            ProductSortEnum::RATING_DESC->value => ['rating', 'desc'],
            ProductSortEnum::RATING_ASC->value => ['rating', 'asc'],
            ProductSortEnum::NAME_ASC->value => ['name', 'asc'],
            ProductSortEnum::NAME_DESC->value => ['name', 'desc'],
            ProductSortEnum::CREATED_AT_DESC->value => ['created_at', 'desc'],
            default => null
        };

        if ($sorting) {
            $query->orderBy(...$sorting);
        }
    }
}
