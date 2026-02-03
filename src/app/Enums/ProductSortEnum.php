<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductSortEnum: string
{
    case PRICE_ASC = 'price_asc';
    case PRICE_DESC = 'price_desc';
    case RATING_ASC = 'rating_asc';
    case RATING_DESC = 'rating_desc';
    case NAME_ASC = 'name_asc';
    case NAME_DESC = 'name_desc';
    case CREATED_AT_DESC = 'created_at_desc';

    //case NEWEST = 'created_at_desc'; // алиас для совместимости

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
