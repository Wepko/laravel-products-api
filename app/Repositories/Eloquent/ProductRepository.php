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
        return Product::query()
            ->with('category')
            ->when($productFilterDTO->q, fn($q) =>
                $q->where('name', 'like', "%{$productFilterDTO->q}%")
            )
            ->paginate($productFilterDTO->perPage);
    }
}
