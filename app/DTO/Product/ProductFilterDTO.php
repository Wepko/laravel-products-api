<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

class ProductFilterDTO extends Data
{
    public function __construct(
        public ?string $q = null,

        #[MapInputName('price_from')]
        public ?float  $priceFrom = null,

        #[MapInputName('price_to')]
        public ?float  $priceTo = null,

        #[MapInputName('category_id')]
        public ?int    $categoryId = null,

        #[MapInputName('in_stock')]
        public ?bool   $inStock = null,

        #[MapInputName('rating_from')]
        public ?float  $ratingFrom = null,

        public ?string $sort = null,

        public int     $page = 1,

        #[MapInputName('per_page')]
        public int     $perPage = 15,
    )
    {
    }
}
