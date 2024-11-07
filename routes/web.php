<?php

use Illuminate\Support\Facades\Route;
use App\Http\controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('products', ProductController::class);