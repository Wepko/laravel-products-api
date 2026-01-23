<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pagination', function () {
    return view('length-away-pagination');
});

Route::get('/pagination/simple', function () {
    return view('products.types.simple-pagination');
});

Route::get('/pagination/cursor', function () {
    return view('products.types.cursor-pagination');
});
//Route::get('/pagination/simple', function () {
//    return view('products.types.simple-pagination');
//});
//Route::get('/pagination/simple', function () {
//    return view('products.types.simple-pagination');
//});
