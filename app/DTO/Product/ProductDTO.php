<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;


class ProductDTO extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public bool $in_stock,
        public float $rating,
        public string $category,
    ) {}
}
