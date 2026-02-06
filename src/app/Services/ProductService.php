<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CategoryDTO;
use App\DTOs\Product\ProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\Filters\ProductFilter;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use phpDocumentor\Reflection\Exception;


class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    )
    {
        $this->repository = new ProductRepository(
            filter: new ProductFilter()
        );
    }

    /**
     * @param ProductFilterDTO $filters
     * @return LengthAwarePaginator
     */
    public function getPaginateProducts(ProductFilterDTO $filters): LengthAwarePaginator
    {
        $paginator = $this->repository->paginate($filters);

        return $paginator;
    }

    /**
     * @param ProductFilterDTO $filters
     * @param string $type
     * @return LengthAwarePaginator|Paginator|CursorPaginator|string
     */
    public function getPaginateProductsBySlug(ProductFilterDTO $filters, string $type): LengthAwarePaginator|Paginator|CursorPaginator|string
    {
        //Todo: use Pattern Strategy
        $paginator = match ($type) {
            'simple' => $this->repository->simplePaginate($filters),
            'custom' => $this->repository->customPaginate($filters),
            'per-page' => $this->repository->paginate($filters),
            'cursor' => $this->repository->cursorPaginate($filters),
            default => throw new \InvalidArgumentException(
                "Unknown pagination type: {$type}. Available types: simple, custom, per-page, cursor"
            )
        };

        $paginator->setCollection(
            $paginator->getCollection()->map(
                fn(Product $product) => new ProductDTO(
                    id: $product->id,
                    name: $product->name,
                    price: (float)$product->price,
                    inStock: $product->in_stock,
                    rating: $product->rating,
                    category: $product->category ? CategoryDTO::from($product->category->toArray()) : null,
                )
            )
        );

        return $paginator;
    }
}

