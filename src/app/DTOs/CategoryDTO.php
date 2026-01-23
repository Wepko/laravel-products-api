<?php

namespace App\DTOs;


use Spatie\LaravelData\Data;

class CategoryDTO extends Data
{
    /**
     * @param string $id
     * @param string $name
     */
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
