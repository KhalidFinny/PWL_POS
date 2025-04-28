<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kategoricontroller; // Typo: Seharusnya KategoriController
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserKontroller; // Typo: Seharusnya UserController
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\DashboardController;

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

// Mengatur pola ID untuk hanya menerima angka
Route::pattern('id', '[0-9]+');

// Rute untuk registrasi
// Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postregister']); // Menangani submit form registrasi

// Rute untuk login
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin']); // Menangani submit form login

// Rute untuk logout, hanya dapat diakses oleh pengguna yang sudah login
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

// Grup rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Rute utama, menampilkan dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rute logout, diulang di dalam grup auth untuk konsistensi
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Grup rute untuk pengelolaan pengguna, hanya untuk admin (ADM)
    Route::group(['prefix' => 'user'], function () {
        Route::middleware(['authorize:ADM'])->group(function () {
            Route::get('/', [UserKontroller::class, 'index']); // Menampilkan daftar pengguna
            Route::post('/list', [UserKontroller::class, 'list']); // Mengambil data pengguna untuk DataTable
            Route::get('/create', [UserKontroller::class, 'create']); // Menampilkan form tambah pengguna
            Route::post('/', [UserKontroller::class, 'store']); // Menyimpan pengguna baru
            Route::get('/create_ajax', [UserKontroller::class, 'create_ajax']); // Form tambah pengguna via AJAX
            Route::post('/ajax', [UserKontroller::class, 'store_ajax']); // Menyimpan pengguna baru via AJAX
            Route::get('/{id}', [UserKontroller::class, 'show']); // Menampilkan detail pengguna
            Route::get('/{id}/edit', [UserKontroller::class, 'edit']); // Menampilkan form edit pengguna
            Route::put('/{id}', [UserKontroller::class, 'update']); // Memperbarui pengguna
            Route::get('/{id}/show_ajax', [UserKontroller::class, 'show_ajax']); // Detail pengguna via AJAX
            Route::get('/{id}/edit_ajax', [UserKontroller::class, 'edit_ajax']); // Form edit pengguna via AJAX
            Route::put('/{id}/update_ajax', [UserKontroller::class, 'update_ajax']); // Memperbarui pengguna via AJAX
            Route::get('/{id}/delete_ajax', [UserKontroller::class, 'confirm_ajax']); // Konfirmasi hapus pengguna via AJAX
            Route::delete('/{id}/delete_ajax', [UserKontroller::class, 'delete_ajax']); // Menghapus pengguna via AJAX
            Route::delete('/{id}', [UserKontroller::class, 'destroy']); // Menghapus pengguna
            Route::get('/import', [UserKontroller::class, 'import']); // Menampilkan form import pengguna
            Route::post('/import_ajax', [UserKontroller::class, 'import_ajax']); // Mengimpor pengguna via AJAX
            Route::get('/export_excel', [UserKontroller::class, 'export_excel']); // Mengekspor pengguna ke Excel
            Route::get('/export_pdf', [UserKontroller::class, 'export_pdf']); // Mengekspor pengguna ke PDF
        });
    });

    // Grup rute untuk pengelolaan level, hanya untuk admin (ADM)
    Route::group(['prefix' => 'level'], function () {
        Route::middleware(['authorize:ADM'])->group(function () {
            Route::get('/', [LevelController::class, 'index']); // Menampilkan daftar level
            Route::post('/list', [LevelController::class, 'list']); // Mengambil data level untuk DataTable
            Route::get('/create', [LevelController::class, 'create']); // Menampilkan form tambah level
            Route::post('/', [LevelController::class, 'store']); // Menyimpan level baru
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']); // Form tambah level via AJAX
            Route::post('/store_ajax', [LevelController::class, 'store_ajax']); // Menyimpan level baru via AJAX
            Route::get('/{id}', [LevelController::class, 'show']); // Menampilkan detail level
            Route::get('/{id}/edit', [LevelController::class, 'edit']); // Menampilkan form edit level
            Route::put('/{id}', [LevelController::class, 'update']); // Memperbarui level
            Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']); // Detail level via AJAX
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']); // Form edit level via AJAX
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']); // Memperbarui level via AJAX
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']); // Konfirmasi hapus level via AJAX
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']); // Menghapus level via AJAX
            Route::delete('/{id}', [LevelController::class, 'destroy']); // Menghapus level
            Route::get('/import', [LevelController::class, 'import']); // Menampilkan form import level
            Route::post('/import_ajax', [LevelController::class, 'import_ajax']); // Mengimpor level via AJAX
            Route::get('/export_excel', [LevelController::class, 'export_excel']); // Mengekspor level ke Excel
            Route::get('/export_pdf', [LevelController::class, 'export_pdf']); // Mengekspor level ke PDF
        });
    });

    // Grup rute untuk pengelolaan barang, akses untuk ADM, MNG, STF, CUS
    Route::group(['prefix' => 'barang'], function () {
        Route::middleware(['authorize:ADM,MNG,STF,CUS'])->group(function () {
            Route::get('/', [BarangController::class, 'index']); // Menampilkan daftar barang
            Route::post('/list', [BarangController::class, 'list']); // Mengambil data barang untuk DataTable
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']); // Form tambah barang via AJAX
            Route::post('/store_ajax', [BarangController::class, 'store_ajax']); // Menyimpan barang baru via AJAX
            Route::get('/{id}', [BarangController::class, 'show']); // Menampilkan detail barang
            Route::get('/{id}/edit', [BarangController::class, 'edit']); // Menampilkan form edit barang
            Route::put('/{id}', [BarangController::class, 'update']); // Memperbarui barang
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']); // Detail barang via AJAX
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); // Form edit barang via AJAX
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']); // Memperbarui barang via AJAX
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']); // Konfirmasi hapus barang via AJAX
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']); // Menghapus barang via AJAX
            Route::delete('/{id}', [BarangController::class, 'destroy']); // Menghapus barang
            Route::get('/import', [BarangController::class, 'import']); // Menampilkan form import barang
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']); // Mengimpor barang via AJAX
            Route::get('/export_excel', [BarangController::class, 'export_excel']); // Mengekspor barang ke Excel
            Route::get('/export_pdf', [BarangController::class, 'export_pdf']); // Mengekspor barang ke PDF
        });
    });

    // Grup rute untuk pengelolaan kategori, akses untuk ADM, MNG, STF
    Route::group(['prefix' => 'kategori'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
            Route::get('/', [KategoriController::class, 'index']); // Menampilkan daftar kategori
            Route::post('/list', [KategoriController::class, 'list']); // Mengambil data kategori untuk DataTable
            Route::get('/create', [KategoriController::class, 'create']); // Menampilkan form tambah kategori
            Route::post('/', [KategoriController::class, 'store']); // Menyimpan kategori baru
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']); // Form tambah kategori via AJAX
            Route::post('/store_ajax', [KategoriController::class, 'store_ajax']); // Menyimpan kategori baru via AJAX
            Route::get('/{id}', [KategoriController::class, 'show']); // Menampilkan detail kategori
            Route::get('/{id}/edit', [KategoriController::class, 'edit']); // Menampilkan form edit kategori
            Route::put('/{id}', [KategoriController::class, 'update']); // Memperbarui kategori
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']); // Detail kategori via AJAX
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']); // Form edit kategori via AJAX
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']); // Memperbarui kategori via AJAX
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']); // Konfirmasi hapus kategori via AJAX
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']); // Menghapus kategori via AJAX
            Route::delete('/{id}', [KategoriController::class, 'destroy']); // Menghapus kategori
            Route::get('/import', [KategoriController::class, 'import']); // Menampilkan form import kategori
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']); // Mengimpor kategori via AJAX
            Route::get('/export_excel', [KategoriController::class, 'export_excel']); // Mengekspor kategori ke Excel
            Route::get('/export_pdf', [KategoriController::class, 'export_pdf']); // Mengekspor kategori ke PDF
        });
    });

    // Grup rute untuk pengelolaan supplier, akses untuk ADM, MNG, STF
    Route::group(['prefix' => 'supplier'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
            Route::get('/', [SupplierController::class, 'index']); // Menampilkan daftar supplier
            Route::post('/list', [SupplierController::class, 'list']); // Mengambil data supplier untuk DataTable
            Route::get('/create', [SupplierController::class, 'create']); // Menampilkan form tambah supplier
            Route::post('/', [SupplierController::class, 'store']); // Menyimpan supplier baru
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']); // Form tambah supplier via AJAX
            Route::post('/store_ajax', [SupplierController::class, 'store_ajax']); // Menyimpan supplier baru via AJAX
            Route::get('/{id}', [SupplierController::class, 'show']); // Menampilkan detail supplier
            Route::get('/{id}/edit', [SupplierController::class, 'edit']); // Menampilkan form edit supplier
            Route::put('/{id}', [SupplierController::class, 'update']); // Memperbarui supplier
            Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']); // Detail supplier via AJAX
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']); // Form edit supplier via AJAX
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']); // Memperbarui supplier via AJAX
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']); // Konfirmasi hapus supplier via AJAX
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']); // Menghapus supplier via AJAX
            Route::delete('/{id}', [SupplierController::class, 'destroy']); // Menghapus supplier
            Route::get('/import', [SupplierController::class, 'import']); // Menampilkan form import supplier
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); // Mengimpor supplier via AJAX
            Route::get('/export_excel', [SupplierController::class, 'export_excel']); // Mengekspor supplier ke Excel
            Route::get('/export_pdf', [SupplierController::class, 'export_pdf']); // Mengekspor supplier ke PDF
        });
    });

    // Grup rute untuk pengelolaan stok, akses untuk ADM, MNG, STF, CUS
    Route::group(['prefix' => 'stok'], function () {
        Route::middleware(['authorize:ADM,MNG,STF,CUS'])->group(function () {
            Route::get('/', [StockController::class, 'index']); // Menampilkan daftar stok
            Route::post('/list', [StockController::class, 'list'])->name('stok.list'); // Mengambil data stok untuk DataTable
            Route::get('/listDelete', [StockController::class, 'listDeleted'])->name('stok.listDelete'); // Menampilkan stok yang dihapus
            Route::get('/{id}', [StockController::class, 'show']); // Menampilkan detail stok
            Route::get('/{id}/show_ajax', [StockController::class, 'show_ajax']); // Detail stok via AJAX

            // Sub-grup rute untuk aksi yang hanya dapat dilakukan oleh ADM dan MNG
            Route::middleware(['authorize:ADM,MNG'])->group(function () {
                Route::post('/increment', [StockController::class, 'increment']); // Menambah jumlah stok
                Route::get('/{id}/edit', [StockController::class, 'edit']); // Menampilkan form edit stok
                Route::put('/{id}', [StockController::class, 'update']); // Memperbarui stok
                Route::get('/{id}/edit_ajax', [StockController::class, 'edit_ajax']); // Form edit stok via AJAX
                Route::put('/{id}/update_ajax', [StockController::class, 'update_ajax']); // Memperbarui stok via AJAX
                Route::get('/{id}/delete_ajax', [StockController::class, 'confirm_ajax']); // Konfirmasi hapus stok via AJAX
                Route::delete('/{id}', [StockController::class, 'destroy']); // Menghapus stok
                Route::get('/import', [StockController::class, 'import']); // Menampilkan form import stok
                Route::post('/import', [StockController::class, 'import_ajax']); // Mengimpor stok via AJAX
                Route::get('/export_excel', [StockController::class, 'export_excel']); // Mengekspor stok ke Excel
                Route::get('/export_pdf', [StockController::class, 'export_pdf']); // Mengekspor stok ke PDF
                Route::post('/list-deleted', [StockController::class, 'listDeleted'])->name('stok.deleted'); // Mengambil data stok yang dihapus
                Route::post('/restock', [StockController::class, 'restock'])->name('stok.restock'); // Merestok barang
            });
        });
    });

    // Grup rute untuk pengelolaan penjualan, akses untuk ADM dan MNG
    Route::group(['prefix' => 'penjualan'], function () {
        Route::middleware(['authorize:ADM,MNG'])->group(function () {
            Route::get('/', [PenjualanController::class, 'index'])->name('penjualan.index'); // Menampilkan daftar penjualan
            Route::post('/list', [PenjualanController::class, 'list']); // Mengambil data penjualan untuk DataTable
            Route::get('/create_ajax', [PenjualanController::class, 'create_ajax']); // Form tambah penjualan via AJAX
            Route::post('/penjualan/store_ajax', [PenjualanController::class, 'store_ajax'])->name('penjualan.store_ajax'); // Menyimpan penjualan baru via AJAX
            Route::get('/{id}', [PenjualanController::class, 'show']); // Menampilkan detail penjualan
            Route::get('/{id}/edit', [PenjualanController::class, 'edit']); // Menampilkan form edit penjualan
            Route::put('/{id}', [PenjualanController::class, 'update'])->name('penjualan.update_ajax'); // Memperbarui penjualan
            Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']); // Konfirmasi hapus penjualan via AJAX
            Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']); // Menghapus penjualan via AJAX
            Route::delete('/{id}', [PenjualanController::class, 'destroy']); // Menghapus penjualan
            Route::get('/import', [PenjualanController::class, 'import']); // Menampilkan form import penjualan
            Route::post('/import_ajax', [PenjualanController::class, 'import_ajax']); // Mengimpor penjualan via AJAX
            Route::get('/export_excel', [PenjualanController::class, 'export_excel']); // Mengekspor penjualan ke Excel

            // Sub-grup rute untuk aksi yang dapat dilakukan oleh ADM, MNG, STF
            Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
                Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax']); // Detail penjualan via AJAX
                Route::get('/export_pdf', [PenjualanController::class, 'export_Pdf'])->name('penjualan.pdf'); // Mengekspor penjualan ke PDF
            });
        });
    });
});
