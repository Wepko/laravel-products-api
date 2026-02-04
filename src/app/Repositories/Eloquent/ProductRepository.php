<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Product\ProductFilterDTO;
use App\Enums\ProductSortEnum;
use App\Filters\ProductFilter;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private ProductFilter $filter
    ) {}

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
            $productFilterDTO->sort = ProductSortEnum::CREATED_AT_DESC->value;
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

        // Применяем фильтры через класс ProductFilter
        $this->filter->apply($query, [
            'inStock' => $filters->inStock,
            'priceFrom' => $filters->priceFrom,
            'priceTo' => $filters->priceTo,
            'categoryId' => $filters->categoryId,
            'ratingFrom' => $filters->ratingFrom,
            'q' => $filters->q,
        ]);

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

    public function customPaginate(ProductFilterDTO $productFilterDTO)
    {
        $query = $this->buildQuery($productFilterDTO);

        if (!$productFilterDTO->sort) {
            $productFilterDTO->sort = ProductSortEnum::CREATED_AT_DESC->value;
        }

        return $query->cursorPaginate(
            $productFilterDTO->perPage,
            ['*'],
            'cursor',
            $productFilterDTO->cursor // Параметр cursor из DTO
        );
    }
}
