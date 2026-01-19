<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductFilterDTO',
    title: 'Фильтр товаров',
    description: 'Параметры фильтрации и сортировки товаров'
)]
class ProductFilterDTO extends Data
{
    public function __construct(
        #[OA\Property(
            description: 'Поисковый запрос по названию товара',
            type: 'string',
            nullable: true
        )]
        public ?string $q = null,

        #[OA\Property(
            description: 'Минимальная цена',
            type: 'number',
            format: 'float',
            example: 100.0,
            nullable: true
        )]
        #[MapInputName('price_from')]
        public ?float  $priceFrom = null,

        #[OA\Property(
            description: 'Максимальная цена',
            type: 'number',
            format: 'float',
            example: 1000.0,
            nullable: true
        )]
        #[MapInputName('price_to')]
        public ?float  $priceTo = null,

        #[OA\Property(
            description: 'ID категории',
            type: 'integer',
            example: 1,
            nullable: true
        )]
        #[MapInputName('category_id')]
        public ?int    $categoryId = null,

        #[OA\Property(
            description: 'Фильтр по наличию на складе',
            type: 'boolean',
            example: true,
            nullable: true
        )]
        #[MapInputName('in_stock')]
        public ?bool   $inStock = null,

        #[OA\Property(
            description: 'Минимальный рейтинг',
            type: 'number',
            format: 'float',
            example: 4.0,
            nullable: true
        )]
        #[MapInputName('rating_from')]
        public ?float  $ratingFrom = null,

        #[OA\Property(
            description: 'Параметр сортировки',
            type: 'string',
            enum: ['price_asc', 'price_desc', 'rating_desc', 'newest'],
            example: 'price_desc',
            nullable: true
        )]
        public ?string $sort = null,

        #[OA\Property(
            description: 'Номер страницы для пагинации',
            type: 'integer',
            default: 1,
            example: 1
        )]
        public int     $page = 1,

        #[OA\Property(
            description: 'Количество товаров на странице',
            type: 'integer',
            default: 15,
            example: 15
        )]
        #[MapInputName('per_page')]
        public int     $perPage = 15,
    )
    {
    }
}
