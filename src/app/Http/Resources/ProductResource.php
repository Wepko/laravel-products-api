<?php

namespace App\Http\Resources;

use App\DTOs\CategoryDTO;
use App\DTOs\Product\ProductDTO;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

// ProductResource.php
class ProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        $dto = new ProductDTO(
            id: $product->id,
            name: $product->name,
            price: (float)$product->price,
            inStock: $product->in_stock,
            rating: $product->rating,
            category: new CategoryDTO(
                id: $product->category->id,
                name: $product->category->name,
            )
        );

        return $dto->toArray();
    }

    public static function getJsonStructure(?Request $request = null): array
    {
        $request = $request ?? Request::create('/');
        $resource = new static((object)[]);

        // Получаем массив ключей из toArray()
        $structure = array_keys($resource->toArray($request));

        // Рекурсивно обрабатываем вложенные структуры
        return array_map(function ($key) use ($resource, $request) {
            // Если это условное поле (when/unless) - нужно обработать особо
            return $key;
        }, $structure);
    }
}

