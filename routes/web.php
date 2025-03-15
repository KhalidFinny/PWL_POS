<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kategoricontroller;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserKontroller;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
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
Route::get('/user', [UserKontroller::class, 'index']);

Route::get('/user/tambah', [UserKontroller::class, 'tambah']);
Route::post('/user/tambah_simpan', [UserKontroller::class, 'tambahSimpan']);
Route::get('/user/ubah/{id}', [UserKontroller::class, 'ubah']);
Route::put('/user/ubah_simpan/{id}', [UserKontroller::class, 'ubah_simpan']);
Route::get('/user/hapus/{id}', [UserKontroller::class, 'hapus']);

Route::get('/', [WelcomeController::class, 'index']);

Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserKontroller::class, 'index']);
    Route::post('/list', [UserKontroller::class, 'list']);
    Route::get('/create', [UserKontroller::class, 'create']);
    Route::post('/', [UserKontroller::class, 'store']);
    Route::get('/create_ajax', [UserKontroller::class, 'create_ajax']);
    Route::post('/ajax', [UserKontroller::class, 'store_ajax']);
    Route::get('/{id}', [UserKontroller::class, 'show']);
    Route::get('/{id}/edit', [UserKontroller::class, 'edit']);
    Route::put('/{id}', [UserKontroller::class, 'update']);
    Route::get('/{id}/edit_ajax', [UserKontroller::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [UserKontroller::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [UserKontroller::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [UserKontroller::class, 'delete_ajax']);
    Route::delete('/{id}', [UserKontroller::class, 'destroy']);
});

Route::group(['prefix' => 'level'], function () {
    Route::get('/level', [LevelController::class, 'index']);
    Route::post('/list', [LevelController::class, 'list']);
    Route::get('/create', [LevelController::class, 'create']);
    Route::post('/', [LevelController::class, 'store']);
    Route::get('/{id}', [LevelController::class, 'show']);
    Route::get('/{id}/edit', [LevelController::class, 'edit']);
    Route::put('/{id}', [LevelController::class, 'update']);
    Route::delete('/{id}', [LevelController::class, 'destroy']);
});

Route::group(['prefix' => 'kategori'], function () {
    Route::get('/kategori', [Kategoricontroller::class, 'index']);
    Route::post('/list', [Kategoricontroller::class, 'list']);
    Route::get('/create', [Kategoricontroller::class, 'create']);
    Route::post('/', [Kategoricontroller::class, 'store']);
    Route::get('/{id}', [Kategoricontroller::class, 'show']);
    Route::get('/{id}/edit', [Kategoricontroller::class, 'edit']);
    Route::put('/{id}', [Kategoricontroller::class, 'update']);
    Route::delete('/{id}', [Kategoricontroller::class, 'destroy']);
});

Route::group(['prefix' => 'barang'], function () {
    Route::get('/', [BarangController::class, 'index']);
    Route::post('/list', [BarangController::class, 'list']);
    Route::get('/create', [BarangController::class, 'create']);
    Route::post('/', [BarangController::class, 'store']);
    Route::get('/{id}', [BarangController::class, 'show']);
    Route::get('/{id}/edit', [BarangController::class, 'edit']);
    Route::put('/{id}', [BarangController::class, 'update']);
    Route::delete('/{id}', [BarangController::class, 'destroy']);
});


Route::group(['prefix' => 'supplier'], function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/list', [SupplierController::class, 'list']);
    Route::get('/create', [SupplierController::class, 'create']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::get('/{id}', [SupplierController::class, 'show']);
    Route::get('/{id}/edit', [SupplierController::class, 'edit']);
    Route::put('/{id}', [SupplierController::class, 'update']);
    Route::delete('/{id}', [SupplierController::class, 'destroy']);
});
