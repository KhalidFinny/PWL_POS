<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\StockModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    public function index()
    {
        Log::debug('PenjualanController@index accessed');

        $breadcrumb = (object) [
            'title' => 'Penjualan',
            'list' => ['Home', 'Data Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar Transaksi Penjualan'
        ];

        $activeMenu = 'penjualan';

        try {
            $users = UserModel::all();
            $stok = StockModel::with(['barang', 'supplier'])
                ->where('stok_jumlah', '>', 0)
                ->get();

            Log::debug('Penjualan index data loaded', [
                'users_count' => count($users),
                'stok_count' => count($stok)
            ]);

            return view('penjualan.index', [
                'breadcrumb' => $breadcrumb,
                'page' => $page,
                'activeMenu' => $activeMenu,
                'users' => $users,
                'stok' => $stok
            ]);

        } catch (\Exception $e) {
            Log::error('Error in PenjualanController@index', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load penjualan index: ' . $e->getMessage());
        }
    }

    public function list()
    {
        Log::debug('PenjualanController@list accessed');

        try {
            $penjualan = PenjualanModel::with(['user', 'details.barang'])
                ->select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal');

            return DataTables::of($penjualan)
                ->addIndexColumn()
                ->addColumn('total', function ($penjualan) {
                    $total = $penjualan->details->sum(function($detail) {
                        return $detail->harga * $detail->jumlah;
                    });
                    Log::debug('Calculating total for penjualan', ['penjualan_id' => $penjualan->penjualan_id, 'total' => $total]);
                    return 'Rp ' . number_format($total, 2);
                })
                ->addColumn('aksi', function ($penjualan) {
                    $btn = '<a href="'.url('/penjualan/'.$penjualan->penjualan_id).'" class="btn btn-info btn-sm">Detail</a> ';;
                    $btn .= '<button class="btn btn-danger btn-sm delete-btn" data-id="'.$penjualan->penjualan_id.'">Delete</button>';
                    return $btn;
                })
                ->editColumn('penjualan_tanggal', function ($penjualan) {
                    $formattedDate = date('d-m-Y H:i', strtotime($penjualan->penjualan_tanggal));
                    Log::debug('Formatting date', [
                        'original' => $penjualan->penjualan_tanggal,
                        'formatted' => $formattedDate
                    ]);
                    return $formattedDate;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in PenjualanController@list', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to load penjualan data'], 500);
        }
    }


    public function create_ajax()
    {
        Log::debug('PenjualanController@create_ajax accessed');

        try {
            $breadcrumb = (object) [
                'title' => 'Tambah Penjualan',
                'list' => ['Home', 'Penjualan', 'Tambah']
            ];

            $page = (object) [
                'title' => 'Tambah transaksi penjualan baru'
            ];

            $activeMenu = 'penjualan';

            $stok = StockModel::with('barang')
                ->where('stok_jumlah', '>', 0)
                ->get();

            Log::debug('Available stok for create_ajax', ['count' => count($stok)]);

            return view('penjualan.create', compact('breadcrumb', 'page', 'activeMenu', 'stok'));

        } catch (\Exception $e) {
            Log::error('Error in PenjualanController@create_ajax', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }
    public function store_ajax(Request $request)
    {
        Log::debug('PenjualanController@store_ajax started', ['request_data' => $request->except('_token')]);

        Log::debug('Authenticated user:', [
            'user' => Auth::user(),
            'user_id' => Auth::id(),
            'is_authenticated' => Auth::check()
        ]);

        $request->validate([
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode',
            'stok_id' => 'required|array',
            'stok_id.*' => 'exists:t_stok,stok_id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            Log::debug('Creating penjualan header');
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            $penjualan = PenjualanModel::create([
                'user_id' => $user->user_id, // Use user_id directly instead of Auth::id()
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => now()
            ]);

            Log::debug('Penjualan header created', ['penjualan_id' => $penjualan->penjualan_id]);

            foreach ($request->stok_id as $key => $stok_id) {
                $stok = StockModel::find($stok_id);
                $jumlah = $request->jumlah[$key];

                Log::debug('Processing stok item', [
                    'stok_id' => $stok_id,
                    'jumlah' => $jumlah,
                    'current_stok' => $stok->stok_jumlah
                ]);

                if ($stok->stok_jumlah < $jumlah) {
                    $errorMsg = "Stok tidak mencukupi untuk barang: " . $stok->barang->barang_nama;
                    Log::error('Insufficient stok', [
                        'stok_id' => $stok_id,
                        'requested' => $jumlah,
                        'available' => $stok->stok_jumlah
                    ]);
                    throw new \Exception($errorMsg);
                }

                DetailPenjualanModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $stok->barang_id,
                    'harga' => $stok->barang->harga_jual,
                    'jumlah' => $jumlah
                ]);

                Log::debug('Detail created', [
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $stok->barang_id,
                    'harga' => $stok->barang->harga_jual,
                    'jumlah' => $jumlah
                ]);

                $stok->decrement('stok_jumlah', $jumlah);
                Log::debug('Stok decremented', [
                    'stok_id' => $stok_id,
                    'new_quantity' => $stok->stok_jumlah
                ]);

                if ($stok->stok_jumlah <= 0) {
                    $stok->delete();
                    Log::debug('Stok soft deleted (reached 0)', ['stok_id' => $stok_id]);
                }

            }

            DB::commit();
            Log::info('Penjualan successfully created', ['penjualan_id' => $penjualan->penjualan_id]);
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan',
                'redirect' => url('/penjualan')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PenjualanController@store_ajax', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan gagal disimpan: ' . $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : []
            ], 422);
        }
    }
    public function show($id)
    {
        Log::debug('PenjualanController@show accessed', ['penjualan_id' => $id]);

        try {
            $penjualan = PenjualanModel::with(['user', 'details.barang'])->findOrFail($id);

            Log::debug('Penjualan details loaded', [
                'penjualan_id' => $penjualan->penjualan_id,
                'detail_count' => count($penjualan->details)
            ]);

            if(request()->has('print')) {
                Log::debug('Showing printable invoice');
                return view('penjualan.show', compact('penjualan'));
            }

            $breadcrumb = (object) [
                'title' => 'Detail Penjualan',
                'list' => ['Home', 'Penjualan', 'Detail']
            ];

            $page = (object) [
                'title' => 'Detail transaksi penjualan'
            ];

            $activeMenu = 'penjualan';

            return view('penjualan.show', compact('penjualan', 'breadcrumb', 'page', 'activeMenu'));
        } catch (\Exception $e) {
            Log::error('Error in PenjualanController@show', [
                'penjualan_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load penjualan details: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penjualan = PenjualanModel::with(['details'])->findOrFail($id);

            // Kembalikan stok untuk setiap detail penjualan
            foreach ($penjualan->details as $detail) {
                $stok = StockModel::where('barang_id', $detail->barang_id)->first();
                if ($stok) {
                    $stok->increment('stok_jumlah', $detail->jumlah);
                }
            }

            // Hapus detail penjualan terlebih dahulu (hard delete)
            DetailPenjualanModel::where('penjualan_id', $id)->delete();

            // Hapus penjualan (hard delete)
            $penjualan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil dihapus permanen'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }
    public function export_pdf()
    {
        $penjualan = DetailPenjualanModel::select('penjualan_id', 'barang_id', 'harga', 'jumlah')
            ->orderBy('penjualan_id')
            ->with('penjualan', 'barang')
            ->get();

        $pdf = Pdf::loadView('penjualan.pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnbled", true);
        $pdf->render();

        return $pdf->stream('Data Barang'.date('Y-m-d').'.pdf');
    }
}
