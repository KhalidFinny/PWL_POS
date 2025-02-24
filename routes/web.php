<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kategoricontroller;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::prefix('category')->group(function () {
    Route::get('food-beverage', [ProductController::class, 'foodBeverage'])->name('products.food-beverage');
    Route::get('beauty-health', [ProductController::class, 'beautyHealth'])->name('products.beauty-health');
    Route::get('home-care', [ProductController::class, 'homeCare'])->name('products.home-care');
    Route::get('baby-kid', [ProductController::class, 'babyKid'])->name('products.baby-kid');
});
Route::get('user/{id}/name/{name}', [UserController::class, 'show'])->name('users.show');
Route::get('/sales', [SalesController::class, 'sales'])->name('sales.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/house', [HomeController::class, 'index'])->name('house');
Route::get('/user', [UserController::class, 'index'])->name('users.index');

Route::get('/level', [LevelController::class, 'index']);
Route::get('/kategori', [Kategoricontroller::class, 'index']);

