<?php

use App\Http\Controllers\AuthController;
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
use Illuminate\Support\Facades\Auth;
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
// */
// Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::prefix('category')->group(function () {
//     Route::get('food-beverage', [ProductController::class, 'foodBeverage'])->name('products.food-beverage');
//     Route::get('beauty-health', [ProductController::class, 'beautyHealth'])->name('products.beauty-health');
//     Route::get('home-care', [ProductController::class, 'homeCare'])->name('products.home-care');
//     Route::get('baby-kid', [ProductController::class, 'babyKid'])->name('products.baby-kid');
// });
// Route::get('user/{id}/name/{name}', [UserController::class, 'show'])->name('users.show');
// Route::get('/sales', [SalesController::class, 'sales'])->name('sales.index');
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/house', [HomeController::class, 'index'])->name('house');
// Route::get('/user', [UserController::class, 'index'])->name('users.index');

// Route::get('/level', [LevelController::class, 'index']);
// Route::get('/kategori', [Kategoricontroller::class, 'index']);
// Route::get('/user', [UserKontroller::class, 'index']);

// Route::get('/user/tambah', [UserKontroller::class, 'tambah']);
// Route::post('/user/tambah_simpan', [UserKontroller::class, 'tambahSimpan']);
// Route::get('/user/ubah/{id}', [UserKontroller::class, 'ubah']);
// Route::put('/user/ubah_simpan/{id}', [UserKontroller::class, 'ubah_simpan']);
// Route::get('/user/hapus/{id}', [UserKontroller::class, 'hapus']);



Route::pattern('id', '[0-9]+');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postregister']);
Route::get('login', [AuthController::class,'login'])->name('login');
Route::post('login', [AuthController::class,'postLogin']);
Route::get('logout', [AuthController::class,'logout'])->middleware('auth');


Route::middleware(['auth'])->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['prefix' => 'user'], function () {
Route::middleware(['authorize:ADM'])->group(function(){
        Route::get('/', [UserKontroller::class, 'index']);
        Route::post('/list', [UserKontroller::class, 'list']);
        Route::get('/create', [UserKontroller::class, 'create']);
        Route::post('/', [UserKontroller::class, 'store']);
        Route::get('/create_ajax', [UserKontroller::class, 'create_ajax']);
        Route::post('/ajax', [UserKontroller::class, 'store_ajax']);
        Route::get('/{id}', [UserKontroller::class, 'show']);
        Route::get('/{id}/edit', [UserKontroller::class, 'edit']);
        Route::put('/{id}', [UserKontroller::class, 'update']);
        Route::get('/{id}/show_ajax', [UserKontroller::class,'show_ajax']);
        Route::get('/{id}/edit_ajax', [UserKontroller::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [UserKontroller::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [UserKontroller::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [UserKontroller::class, 'delete_ajax']);
        Route::delete('/{id}', [UserKontroller::class, 'destroy']);
        Route::get('/import', [UserKontroller::class, 'import']);
        Route::post('/import_ajax', [UserKontroller::class, 'import_ajax']);
    });
});


    Route::group(['prefix' => 'level'], function () {
    Route::middleware(['authorize:ADM'])->group(function () {
        Route::get('/', [LevelController::class, 'index']);
        Route::post('/list', [LevelController::class, 'list']);
        Route::get('/create', [LevelController::class, 'create']);
        Route::post('/', [LevelController::class, 'store']);
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
        Route::post('/store_ajax', [LevelController::class, 'store_ajax']);
        Route::get('/{id}', [LevelController::class, 'show']);
        Route::get('/{id}/edit', [LevelController::class, 'edit']);
        Route::put('/{id}', [LevelController::class, 'update']);
        Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
        Route::delete('/{id}', [LevelController::class, 'destroy']);
        Route::get('/import', [LevelController::class, 'import']);
        Route::post('/import_ajax', [LevelController::class, 'import_ajax']);
    });
});

    Route::group(['prefix' => 'barang'], function () {
        Route::middleware(['authorize:ADM,MNG,STF,CUS'])->group(function () {
        Route::get('/', [BarangController::class, 'index']);
        Route::post('/list', [BarangController::class, 'list']);
        Route::get('/create', [BarangController::class, 'create']);
        Route::post('/', [BarangController::class, 'store']);
        Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
        Route::post('/store_ajax', [BarangController::class, 'store_ajax']);
        Route::get('/{id}', [BarangController::class, 'show']);
        Route::get('/{id}/edit', [BarangController::class, 'edit']);
        Route::put('/{id}', [BarangController::class, 'update']);
        Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
        Route::delete('/{id}', [BarangController::class, 'destroy']);
        Route::get('/import', [Barangcontroller::class, 'import']);
        Route::post('/import_ajax', [Barangcontroller::class, 'import_ajax']);
    });
});

    Route::group(['prefix' => 'kategori'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::get('/', [KategoriController::class, 'index']);
        Route::post('/list', [KategoriController::class, 'list']);
        Route::get('/create', [KategoriController::class, 'create']);
        Route::post('/', [KategoriController::class, 'store']);
        Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);
        Route::post('/store_ajax', [KategoriController::class, 'store_ajax']);
        Route::get('/{id}', [KategoriController::class, 'show']);
        Route::get('/{id}/edit', [KategoriController::class, 'edit']);
        Route::put('/{id}', [KategoriController::class, 'update']);
        Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
        Route::delete('/{id}', [KategoriController::class, 'destroy']);
        Route::get('/import', [KategoriController::class, 'import']);
        Route::post('/import_ajax', [KategoriController::class, 'import_ajax']);
    });
    });


    Route::group(['prefix' => 'supplier'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/list', [SupplierController::class, 'list']);
        Route::get('/create', [SupplierController::class, 'create']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);
        Route::post('/store_ajax', [SupplierController::class, 'store_ajax']);
        Route::get('/{id}', [SupplierController::class, 'show']);
        Route::get('/{id}/edit', [SupplierController::class, 'edit']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
        Route::get('/import', [SupplierController::class, 'import']);
        Route::post('/import_ajax', [SupplierController::class, 'import_ajax']);
    });
    });

});
