<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Сначала обновим внешний ключ, чтобы он разрешал нулевые значения
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            // Добавим новую колонку uuid
            $table->uuid('uuid')->nullable()->after('id');

            // Добавим новую колонку для связи с категориями через uuid
            $table->uuid('category_uuid')->nullable()->after('category_id');
        });

        // Заполним uuid для существующих записей
        DB::table('products')->get()->each(function ($product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'uuid' => (string) Illuminate\Support\Str::uuid(),
                    'category_uuid' => DB::table('categories')
                        ->where('id', $product->category_id)
                        ->value('uuid')
                ]);
        });

        Schema::table('products', function (Blueprint $table) {
            // Убедимся, что все uuid заполнены
            $table->uuid('uuid')->nullable(false)->change();
            $table->uuid('category_uuid')->nullable(false)->change();

            // Добавим уникальный индекс для uuid
            $table->unique('uuid');

            // Добавим индекс для category_uuid
            $table->index('category_uuid');

            // Восстановим внешний ключ, но теперь через uuid
            $table->foreign('category_uuid')
                ->references('uuid')
                ->on('categories')
                ->cascadeOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Удалим внешний ключ
            $table->dropForeign(['category_uuid']);

            // Удалим индексы
            $table->dropIndex(['category_uuid']);
            $table->dropUnique(['uuid']);

            // Удалим колонки
            $table->dropColumn('uuid');
            $table->dropColumn('category_uuid');

            // Восстановим старый внешний ключ
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();
        });
    }
};
