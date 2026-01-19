<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
