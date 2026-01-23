<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Product\ProductFilterDTO;
//use App\Repositories\Specifications\Product\ProductSpecification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

interface ProductRepositoryInterface
{
    public function paginate(ProductFilterDTO $productFilterDTO): LengthAwarePaginator;

    public function simplePaginate(ProductFilterDTO $productFilterDTO): Paginator;

    public function cursorPaginate(ProductFilterDTO $productFilterDTO): CursorPaginator;

//    public function findBySpecification(ProductSpecification $specification): LengthAwarePaginator;
}
