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
        Schema::table('categories', function (Blueprint $table) {
            // Сначала добавим новую колонку uuid
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Заполним uuid для существующих записей
        DB::table('categories')->get()->each(function ($category) {
            DB::table('categories')
                ->where('id', $category->id)
                ->update(['uuid' => (string) Illuminate\Support\Str::uuid()]);
        });

        Schema::table('categories', function (Blueprint $table) {
            // Убедимся, что все uuid заполнены
            $table->uuid('uuid')->nullable(false)->change();

            // Добавим уникальный индекс
            $table->unique('uuid');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
