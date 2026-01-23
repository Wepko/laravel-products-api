<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем тестового пользователя
//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        // Создаем категории
        $categories = Category::factory(10)->create();

        // Настройки для пакетной вставки
        $chunkSize = 1000; // Размер пачки для вставки
        $totalProducts = 1000000; // Общее количество товаров
        $productsPerCategory = 100000; // По 100,000 товаров на категорию

        foreach ($categories as $category) {
            for ($i = 0; $i < $productsPerCategory / $chunkSize; $i++) {
                $products = [];

                for ($j = 0; $j < $chunkSize; $j++) {
                    $products[] = [
                        'category_id' => $category->id,
                        'name' => fake()->words(3, true),
                        'price' => fake()->randomFloat(2, 1, 10000),
                        'in_stock' => mt_rand(1, 100) <= 80 ? 1 : 0,
                        'rating' => mt_rand(0, 50) / 10,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Product::insert($products);

                $currentCount = ($i + 1) * $chunkSize;
                echo "Создано товаров для категории {$category->id}: {$currentCount}/{$productsPerCategory}\n";
            }
        }

        echo "Всего создано товаров: " . Product::count() . "\n";
    }
}
