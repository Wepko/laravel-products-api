<?php

namespace App\Repositories\Eloquent;

use App\DTO\Product\ProductFilterDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(ProductFilterDTO $productFilterDTO): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category');

        // Фильтр по наличию
        if ($productFilterDTO->inStock !== null) {
            $query->where('in_stock', $productFilterDTO->inStock);
        }

        // Фильтр по цене от
        if ($productFilterDTO->priceFrom !== null) {
            $query->where('price', '>=', $productFilterDTO->priceFrom);
        }

        // Фильтр по цене до
        if ($productFilterDTO->priceTo !== null) {
            $query->where('price', '<=', $productFilterDTO->priceTo);
        }

        // Фильтр по категории
        if ($productFilterDTO->categoryId !== null) {
            $query->where('category_id', $productFilterDTO->categoryId);
        }

        // Фильтр по рейтингу от
        if ($productFilterDTO->ratingFrom !== null) {
            $query->where('rating', '>=', $productFilterDTO->ratingFrom);
        }

        // Поиск по названию
        if ($productFilterDTO->q) {
            $query->where('name', 'like', "%{$productFilterDTO->q}%");
        }

        // Сортировка
        if ($productFilterDTO->sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($productFilterDTO->sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($productFilterDTO->sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($productFilterDTO->sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($productFilterDTO->sort === 'created_at_desc') {
            $query->orderBy('created_at', 'desc');
        }

        // Пагинация
        return $query->paginate(
            $productFilterDTO->perPage,
            ['*'],
            'page',
            $productFilterDTO->page
        );
    }
}
