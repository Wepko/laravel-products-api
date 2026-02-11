<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
//            $table->dropIndex(['price']);
//            $table->dropIndex(['category_uuid']);
//            $table->dropIndex(['in_stock']);
//            $table->dropIndex(['rating']);
//            $table->dropIndex(['created_at']);
//
//            // Порядок ВАЖЕН: equality → sort/range (ESR)
//            $table->index(['category_uuid', 'in_stock', 'price', 'rating']);
//
//            // 2. Индекс для сортировки по новизне
//            $table->index(['created_at', 'id']); // id для уникальности сортировки
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'in_stock', 'price', 'rating']);
            $table->dropIndex(['created_at', 'id']);

            $table->index('price');
            $table->index('category_id');
            $table->index('in_stock');
            $table->index('rating');
            $table->index('created_at');
        });
    }
};
