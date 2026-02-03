<?php

namespace App\Http\Resources;

use App\DTOs\Product\ProductDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// ProductResource.php
class ProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        /** @var ProductDTO $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'in_stock' => (bool) $this->inStock,
            'rating' => (float) $this->rating,
            'category' => $this->category,
        ];
    }

    public static function getJsonStructure(?Request $request = null): array
    {
        $request = $request ?? Request::create('/');
        $resource = new static((object) []);

        // Получаем массив ключей из toArray()
        $structure = array_keys($resource->toArray($request));

        // Рекурсивно обрабатываем вложенные структуры
        return array_map(function ($key) use ($resource, $request) {
            // Если это условное поле (when/unless) - нужно обработать особо
            return $key;
        }, $structure);
    }
}

