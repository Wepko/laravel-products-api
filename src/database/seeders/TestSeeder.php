<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // Очистка таблиц
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();    // сначала товары
        Category::truncate();   // потом категории
        User::truncate();       // потом пользователи
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Создаем тестового пользователя
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Создаем категории
        $categories = Category::factory(10)->create();

        // Настройки
        $chunkSize = 1000; // Увеличим размер пачки для скорости
        $productsPerCategory = 100000; // 100k товаров на категорию
        $totalChunks = $productsPerCategory / $chunkSize; // 100 пачек по 1000

        echo "Начинаем создание 1 000 000 товаров...\n";

        foreach ($categories as $category) {
            echo "Категория {$category->name} (ID: {$category->id}):\n";

            for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
                $products = [];
                $now = now();

                for ($i = 0; $i < $chunkSize; $i++) {
                    $products[] = [
                        'id' => Str::ulid()->toRfc4122(),
                        'category_id' => $category->id,
                        'name' => implode(' ', fake()->words(3)),
                        'price' => round(mt_rand(100, 1000000) / 100, 2),
                        'rating' => round(mt_rand(0, 50) / 10, 1), // 0.0 - 5.0
                        'in_stock' => mt_rand(0, 1),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Используем DB::table вместо модели для скорости
                DB::table('products')->insert($products);

                // Освобождаем память
                unset($products);

                // Выводим прогресс каждые 10 пачек
                if (($chunk + 1) % 10 === 0) {
                    $created = ($chunk + 1) * $chunkSize;
                    echo "  Создано: {$created}/{$productsPerCategory}\n";

                    // Сбрасываем соединение для очистки памяти (правильный способ)
                    DB::purge('mysql');
                    DB::reconnect('mysql');
                }
            }

            echo "  Категория завершена: {$productsPerCategory} товаров\n";

            // Освобождаем память после каждой категории
            gc_collect_cycles();
        }

        $totalCount = DB::table('products')->count();
        echo "\n✅ Готово! Всего создано товаров: {$totalCount}\n";
    }
}
