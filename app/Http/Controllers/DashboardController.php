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
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list' => ['Home']
        ];

        $page = (object) [
            'title' => 'Dashboard'
        ];

        $activeMenu = 'Dashboard';
        $totalStockItems = StockModel::sum('stok_jumlah');
        $totalProducts = BarangModel::count();

        // Get low stock items (less than 5 units)
        $lowStockCount = StockModel::where('stok_jumlah', '<', 5)->count();
        $lowStockItems = StockModel::with(['barang.kategori', 'supplier', 'user'])
            ->where('stok_jumlah', '<', 5)
            ->orderBy('stok_jumlah', 'asc')
            ->take(10)
            ->get();

        // Get recent activities
        $recentActivities = StockModel::with(['barang', 'supplier', 'user'])
            ->orderBy('stok_tanggal', 'desc')
            ->take(5)
            ->get();

        // Get top products by stock
        $topProducts = DB::table('m_barang')
        ->select('m_barang.*', DB::raw('SUM(t_stok.stok_jumlah) as total_stock'))
        ->leftJoin('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
        ->groupBy('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.brang_kode',
                  'm_barang.kategori_id', 'm_barang.supplier_id', 'm_barang.harga_beli',
                  'm_barang.harga_jual', 'm_barang.created_at', 'm_barang.updated_at')
        ->orderBy('total_stock', 'desc')
        ->take(5)
        ->get();

        // Prepare chart data
        $stockChartData = DB::table('m_barang')
        ->select('m_barang.barang_nama', DB::raw('SUM(t_stok.stok_jumlah) as total_stock'))
        ->leftJoin('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
        ->groupBy('m_barang.barang_id', 'm_barang.barang_nama')
        ->orderBy('total_stock', 'desc')
        ->take(10)
        ->get();

        $chartData = [
            'labels' => $stockChartData->pluck('barang_nama')->toArray(),
            'data' => $stockChartData->pluck('total_stock')->toArray(),
        ];

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
