<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

class ProductFilterDTO extends Data
{
    public function __construct(
        public ?string $q,

        #[MapInputName('price_from')]
        public ?float  $priceFrom,

        #[MapInputName('price_to')]
        public ?float  $priceTo,

        public ?int    $category_id,

        public ?bool   $in_stock,

        #[MapInputName('rating_from')]
        public ?float  $ratingFrom,

        public ?string $sort,

        public int     $page = 1,

        #[MapInputName('per_page')]
        public int     $perPage = 15,
    )
    {
    }
}
