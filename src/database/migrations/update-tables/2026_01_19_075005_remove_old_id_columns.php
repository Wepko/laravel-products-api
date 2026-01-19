<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Удаляем старые колонки и индексы
            $table->dropColumn('category_id');
            $table->dropColumn('id');

            // Переименовываем uuid в id
            $table->renameColumn('uuid', 'id');
            $table->renameColumn('category_uuid', 'category_id');

            // Делаем id первичным ключом
            $table->primary('id');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Удаляем старый id
            $table->dropColumn('id');

            // Переименовываем uuid в id
            $table->renameColumn('uuid', 'id');

            // Делаем id первичным ключом
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Восстанавливаем старый id
            $table->dropPrimary(['id']);
            $table->renameColumn('id', 'uuid');
            $table->id()->first();
        });

        Schema::table('products', function (Blueprint $table) {
            // Восстанавливаем структуру
            $table->dropPrimary(['id']);
            $table->renameColumn('id', 'uuid');
            $table->renameColumn('category_id', 'category_uuid');

            // Добавляем старые колонки
            $table->id()->first();
            $table->foreignId('category_id')->nullable()->after('uuid');

            // Восстанавливаем связь
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();
        });
    }
};
