<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductDTO;
use App\DTO\Product\ProductFilterDTO;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


readonly class ProductService
{
    public function __construct(
        private ProductRepository $repository
    ) {}

    public function getPaginateProducts(ProductFilterDTO $filters): LengthAwarePaginator
    {
        $paginator = $this->repository->paginate($filters);

        $paginator->setCollection(
            $paginator->getCollection()->map(
                fn ($product) => new ProductDTO(
                    id: $product->id,
                    name: $product->name,
                    price: $product->price,
                    in_stock: $product->in_stock,
                    rating: $product->rating,
                    category: $product->category,
                )
            )
        );

        return $paginator;
    }
}

