<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data statistik dan grafik.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Data breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home']
        ];

        // Data judul halaman
        $page = (object) [
            'title' => 'Dashboard'
        ];

        // Menandai menu aktif di sidebar
        $activeMenu = 'Dashboard';

        // Menghitung total jumlah stok dari semua item
        $totalStockItems = StockModel::sum('stok_jumlah');

        // Menghitung total jumlah produk
        $totalProducts = BarangModel::count();

        // Menghitung jumlah item dengan stok rendah (kurang dari 5 unit)
        $lowStockCount = StockModel::where('stok_jumlah', '<', 5)->count();

        // Mengambil 10 item dengan stok rendah, termasuk relasi barang, kategori, supplier, dan user
        $lowStockItems = StockModel::with(['barang.kategori', 'supplier', 'user'])
            ->where('stok_jumlah', '<', 5)
            ->orderBy('stok_jumlah', 'asc')
            ->take(10)
            ->get();

        // Mengambil 5 aktivitas stok terbaru, termasuk relasi barang, supplier, dan user
        $recentActivities = StockModel::with(['barang', 'supplier', 'user'])
            ->orderBy('stok_tanggal', 'desc')
            ->take(5)
            ->get();

        // Mengambil 5 produk dengan stok tertinggi menggunakan query langsung
        $topProducts = DB::table('m_barang')
            ->select(
                'm_barang.*',
                DB::raw('SUM(t_stok.stok_jumlah) as total_stock')
            )
            ->leftJoin('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
            ->groupBy(
                'm_barang.barang_id',
                'm_barang.barang_nama',
                'm_barang.brang_kode', // Typo: Seharusnya barang_kode
                'm_barang.kategori_id',
                'm_barang.supplier_id',
                'm_barang.harga_beli',
                'm_barang.harga_jual',
                'm_barang.created_at',
                'm_barang.updated_at',
                'm_barang.image'
            )
            ->orderBy('total_stock', 'desc')
            ->take(5)
            ->get();

        // Menyiapkan data untuk grafik stok
        $stockChartData = DB::table('m_barang')
            ->select(
                'm_barang.barang_nama',
                DB::raw('SUM(t_stok.stok_jumlah) as total_stock')
            )
            ->leftJoin('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
            ->groupBy('m_barang.barang_id', 'm_barang.barang_nama')
            ->orderBy('total_stock', 'desc')
            ->take(10)
            ->get();

        // Format data grafik untuk digunakan di frontend
        $chartData = [
            'labels' => $stockChartData->pluck('barang_nama')->toArray(),
            'data' => $stockChartData->pluck('total_stock')->toArray(),
        ];

        // Mengembalikan view welcome dengan semua data
        return view('welcome', compact(
            'totalStockItems',
            'totalProducts',
            'lowStockCount',
            'lowStockItems',
            'recentActivities',
            'topProducts',
            'chartData'
        ))->with(compact(
            'breadcrumb',
            'page',
            'activeMenu'
        ));
    }
}
