<?php

namespace App\Repositories\Contracts;

use App\DTO\Product\ProductFilterDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(ProductFilterDTO $productFilterDTO): LengthAwarePaginator;
}
