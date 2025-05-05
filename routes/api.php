<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DebugJWTMiddleware;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\LevelControllers;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PenjualanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', \App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/register1', \App\Http\Controllers\Api\RegisterController::class)->name('register1');
Route::post('/login', \App\Http\Controllers\Api\LoginController::class)->name('login');
Route::post('/logout', \App\Http\Controllers\Api\LogoutController::class)->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    $user = $request->user();
    Log::info('User from request (auth:api): ' . json_encode($user));
    return $user ?? response()->json(['error' => 'User not found'], 404);
});

Route::get('levels', [LevelControllers::class, 'index']);
Route::post('levels', [LevelControllers::class, 'store']);
Route::get('levels/{level}', [LevelControllers::class, 'show']);
Route::put('levels/{level}', [LevelControllers::class, 'update']);
Route::delete('levels/{level}', [LevelControllers::class, 'destroy']);


Route::get('barang', [BarangController::class, 'index']);
Route::post('barang', [BarangController::class, 'store']);
Route::get('barang/{barang}', [BarangController::class, 'show']);
Route::put('barang/{barang}', [BarangController::class, 'update']);
Route::delete('barang/{barang}', [BarangController::class, 'destroy']);

Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);

// Kategori API
Route::get('kategori', [KategoriController::class, 'index']);
Route::post('kategori', [KategoriController::class, 'store']);
Route::get('kategori/{kategori}', [KategoriController::class, 'show']);
Route::put('kategori/{kategori}', [KategoriController::class, 'update']);
Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy']);

Route::get('penjualan', [PenjualanController::class, 'index']);
Route::post('penjualan', [PenjualanController::class, 'store']);
Route::get('penjualan/{penjualan}', [PenjualanController::class, 'show']);
Route::put('penjualan/{penjualan}', [PenjualanController::class, 'update']);
Route::delete('penjualan/{penjualan}', [PenjualanController::class, 'destroy']);
