<?php

namespace App\DTOs\Product;

use App\DTOs\CategoryDTO;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

class ProductDTO extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param float $price
     * @param bool $inStock
     * @param float $rating
     * @param CategoryDTO $category
     */
    public function __construct(
        public string $id,
        public string $name,
        public float $price,

        #[MapOutputName('in_stock')] // Для named arguments
        public bool $inStock,

        public float $rating,
        public CategoryDTO $category,
    ) {}
}
