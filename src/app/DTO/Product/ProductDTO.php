<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;


class ProductDTO extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param float $price
     * @param bool $inStock
     * @param float $rating
     * @param string $category
     */
    public function __construct(
        public string $id,

        public string $name,

        public float  $price,

        #[MapInputName('in_stock')]
        public bool   $inStock,

        public float  $rating,

        public string $category,
    )
    {
    }
}
