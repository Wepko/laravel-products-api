<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 5000),
            'in_stock' => $this->faker->boolean(80),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'category_id' => Category::factory(),
        ];
    }
}
