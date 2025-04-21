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
    /**
     * Menampilkan halaman utama daftar transaksi penjualan
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Log untuk debugging akses ke method index
        Log::debug('PenjualanController@index accessed');

        // Membuat objek breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Penjualan',
            'list' => ['Home', 'Data Penjualan']
        ];

        // Membuat objek page untuk judul halaman
        $page = (object) [
            'title' => 'Daftar Transaksi Penjualan'
        ];

        // Menentukan menu aktif
        $activeMenu = 'penjualan';

        try {
            // Mengambil semua data user dari tabel t_user
            $users = UserModel::all();
            // Mengambil data stok yang memiliki jumlah > 0, dengan relasi barang dan supplier
            $stok = StockModel::with(['barang', 'supplier'])
                ->where('stok_jumlah', '>', 0)
                ->get();

            // Log untuk memastikan data berhasil dimuat
            Log::debug('Penjualan index data loaded', [
                'users_count' => count($users),
                'stok_count' => count($stok)
            ]);

            // Mengembalikan view penjualan.index dengan data yang diperlukan
            return view('penjualan.index', [
                'breadcrumb' => $breadcrumb,
                'page' => $page,
                'activeMenu' => $activeMenu,
                'users' => $users,
                'stok' => $stok
            ]);

        } catch (\Exception $e) {
            // Log error jika terjadi masalah saat memuat data
            Log::error('Error in PenjualanController@index', ['error' => $e->getMessage()]);
            // Redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Failed to load penjualan index: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil data penjualan untuk ditampilkan dalam DataTables
     * @return \Yajra\DataTables\Facades\DataTables
     */
    public function list()
    {
        // Log untuk debugging akses ke method list
        Log::debug('PenjualanController@list accessed');

        try {
            // Mengambil data penjualan dengan relasi user dan detail barang
            $penjualan = PenjualanModel::with(['user', 'details.barang'])
                ->select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal');

            // Mengembalikan data dalam format DataTables
            return DataTables::of($penjualan)
                ->addIndexColumn() // Menambahkan kolom nomor urut
                ->addColumn('total', function ($penjualan) {
                    // Menghitung total harga berdasarkan detail penjualan (harga * jumlah)
                    $total = $penjualan->details->sum(function($detail) {
                        return $detail->harga * $detail->jumlah;
                    });
                    // Log untuk memastikan perhitungan total benar
                    Log::debug('Calculating total for penjualan', ['penjualan_id' => $penjualan->penjualan_id, 'total' => $total]);
                    return 'Rp ' . number_format($total, 2); // Format total dengan Rp
                })
                ->addColumn('aksi', function ($penjualan) {
                    // Menambahkan tombol aksi untuk detail dan hapus
                    $btn = '<a href="'.url('/penjualan/'.$penjualan->penjualan_id).'" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<button class="btn btn-danger btn-sm delete-btn" data-id="'.$penjualan->penjualan_id.'">Delete</button>';
                    return $btn;
                })
                ->editColumn('penjualan_tanggal', function ($penjualan) {
                    // Memformat tanggal penjualan ke format d-m-Y H:i
                    $formattedDate = date('d-m-Y H:i', strtotime($penjualan->penjualan_tanggal));
                    // Log untuk memastikan format tanggal benar
                    Log::debug('Formatting date', [
                        'original' => $penjualan->penjualan_tanggal,
                        'formatted' => $formattedDate
                    ]);
                    return $formattedDate;
                })
                ->rawColumns(['aksi']) // Mengizinkan HTML pada kolom aksi
                ->make(true);
        } catch (\Exception $e) {
            // Log error jika terjadi masalah saat memuat data
            Log::error('Error in PenjualanController@list', ['error' => $e->getMessage()]);
            // Mengembalikan respons JSON dengan pesan error
            return response()->json(['error' => 'Failed to load penjualan data'], 500);
        }
    }

    /**
     * Menampilkan form tambah penjualan melalui AJAX
     * @return \Illuminate\View\View
     */
    public function create_ajax()
    {
        // Log untuk debugging akses ke method create_ajax
        Log::debug('PenjualanController@create_ajax accessed');

        try {
            // Membuat objek breadcrumb untuk navigasi
            $breadcrumb = (object) [
                'title' => 'Tambah Penjualan',
                'list' => ['Home', 'Penjualan', 'Tambah']
            ];

            // Membuat objek page untuk judul halaman
            $page = (object) [
                'title' => 'Tambah transaksi penjualan baru'
            ];

            // Menentukan menu aktif
            $activeMenu = 'penjualan';

            // Mengambil data stok yang memiliki jumlah > 0 dengan relasi barang
            $stok = StockModel::with('barang')
                ->where('stok_jumlah', '>', 0)
                ->get();

            // Log untuk memastikan stok tersedia
            Log::debug('Available stok for create_ajax', ['count' => count($stok)]);

            // Mengembalikan view penjualan.create dengan data yang diperlukan
            return view('penjualan.create', compact('breadcrumb', 'page', 'activeMenu', 'stok'));

        } catch (\Exception $e) {
            // Log error jika terjadi masalah saat memuat form
            Log::error('Error in PenjualanController@create_ajax', ['error' => $e->getMessage()]);
            // Redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan data penjualan baru melalui AJAX
     * @param Request $request Data input dari form
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_ajax(Request $request)
    {
        // Log untuk debugging data yang diterima
        Log::debug('PenjualanController@store_ajax started', ['request_data' => $request->except('_token')]);

        // Log untuk memastikan user terautentikasi
        Log::debug('Authenticated user:', [
            'user' => Auth::user(),
            'user_id' => Auth::id(),
            'is_authenticated' => Auth::check()
        ]);

        // Validasi input dari form
        $request->validate([
            'pembeli' => 'required|string|max:100', // Nama pembeli wajib diisi, maks 100 karakter
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode', // Kode penjualan wajib unik
            'stok_id' => 'required|array', // Stok ID wajib berupa array
            'stok_id.*' => 'exists:t_stok,stok_id', // Setiap stok ID harus ada di tabel stok
            'jumlah' => 'required|array', // Jumlah wajib berupa array
            'jumlah.*' => 'integer|min:1' // Setiap jumlah harus integer dan minimal 1
        ]);

        // Memulai transaksi database
        DB::beginTransaction();
        try {
            // Memastikan user terautentikasi
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            // Membuat header penjualan baru
            $penjualan = PenjualanModel::create([
                'user_id' => $user->user_id, // Menggunakan user_id dari model user
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => now()
            ]);

            // Log untuk memastikan header penjualan berhasil dibuat
            Log::debug('Penjualan header created', ['penjualan_id' => $penjualan->penjualan_id]);

            // Memproses setiap item stok yang dipilih
            foreach ($request->stok_id as $key => $stok_id) {
                // Mengambil data stok berdasarkan stok_id
                $stok = StockModel::find($stok_id);
                $jumlah = $request->jumlah[$key];

                // Log untuk memantau proses stok
                Log::debug('Processing stok item', [
                    'stok_id' => $stok_id,
                    'jumlah' => $jumlah,
                    'current_stok' => $stok->stok_jumlah
                ]);

                // Memeriksa apakah stok cukup
                if ($stok->stok_jumlah < $jumlah) {
                    $errorMsg = "Stok tidak mencukupi untuk barang: " . $stok->barang->barang_nama;
                    Log::error('Insufficient stok', [
                        'stok_id' => $stok_id,
                        'requested' => $jumlah,
                        'available' => $stok->stok_jumlah
                    ]);
                    throw new \Exception($errorMsg);
                }

                // Membuat detail penjualan untuk setiap item
                DetailPenjualanModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $stok->barang_id,
                    'harga' => $stok->barang->harga_jual,
                    'jumlah' => $jumlah
                ]);

                // Log untuk memastikan detail penjualan berhasil dibuat
                Log::debug('Detail created', [
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $stok->barang_id,
                    'harga' => $stok->barang->harga_jual,
                    'jumlah' => $jumlah
                ]);

                // Mengurangi jumlah stok
                $stok->decrement('stok_jumlah', $jumlah);
                // Log untuk memastikan stok berhasil dikurangi
                Log::debug('Stok decremented', [
                    'stok_id' => $stok_id,
                    'new_quantity' => $stok->stok_jumlah
                ]);

                // Menghapus stok jika jumlahnya mencapai 0
                if ($stok->stok_jumlah <= 0) {
                    $stok->delete();
                    // Log untuk memastikan stok dihapus
                    Log::debug('Stok soft deleted (reached 0)', ['stok_id' => $stok_id]);
                }
            }

            // Commit transaksi jika semua proses berhasil
            DB::commit();
            // Log untuk konfirmasi penjualan berhasil
            Log::info('Penjualan successfully created', ['penjualan_id' => $penjualan->penjualan_id]);
            // Mengembalikan respons JSON sukses
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan',
                'redirect' => url('/penjualan')
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            // Log error dengan detail
            Log::error('Error in PenjualanController@store_ajax', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Mengembalikan respons JSON dengan pesan error
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan gagal disimpan: ' . $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : []
            ], 422);
        }
    }

    /**
     * Menampilkan detail transaksi penjualan
     * @param int $id ID penjualan
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Log untuk debugging akses ke method show
        Log::debug('PenjualanController@show accessed', ['penjualan_id' => $id]);

        try {
            // Mengambil data penjualan dengan relasi user dan detail barang
            $penjualan = PenjualanModel::with(['user', 'details.barang'])->findOrFail($id);

            // Log untuk memastikan data penjualan berhasil dimuat
            Log::debug('Penjualan details loaded', [
                'penjualan_id' => $penjualan->penjualan_id,
                'detail_count' => count($penjualan->details)
            ]);

            // Jika ada parameter print, tampilkan view untuk cetak invoice
            if (request()->has('print')) {
                Log::debug('Showing printable invoice');
                return view('penjualan.show', compact('penjualan'));
            }

            // Membuat objek breadcrumb untuk navigasi
            $breadcrumb = (object) [
                'title' => 'Detail Penjualan',
                'list' => ['Home', 'Penjualan', 'Detail']
            ];

            // Membuat objek page untuk judul halaman
            $page = (object) [
                'title' => 'Detail transaksi penjualan'
            ];

            // Menentukan menu aktif
            $activeMenu = 'penjualan';

            // Mengembalikan view penjualan.show dengan data yang diperlukan
            return view('penjualan.show', compact('penjualan', 'breadcrumb', 'page', 'activeMenu'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah saat memuat detail
            Log::error('Error in PenjualanController@show', [
                'penjualan_id' => $id,
                'error' => $e->getMessage()
            ]);
            // Redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Failed to load penjualan details: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data penjualan dan mengembalikan stok
     * @param int $id ID penjualan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Memulai transaksi database
        DB::beginTransaction();
        try {
            // Mengambil data penjualan dengan relasi detail
            $penjualan = PenjualanModel::with(['details'])->findOrFail($id);

            // Mengembalikan stok untuk setiap detail penjualan
            foreach ($penjualan->details as $detail) {
                // Mencari stok berdasarkan barang_id
                $stok = StockModel::where('barang_id', $detail->barang_id)->first();
                if ($stok) {
                    // Menambah jumlah stok sesuai jumlah di detail penjualan
                    $stok->increment('stok_jumlah', $detail->jumlah);
                }
            }

            // Menghapus detail penjualan (hard delete)
            DetailPenjualanModel::where('penjualan_id', $id)->delete();

            // Menghapus data penjualan (hard delete)
            $penjualan->delete();

            // Commit transaksi jika semua proses berhasil
            DB::commit();

            // Mengembalikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil dihapus permanen'
            ]);

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            // Mengembalikan respons JSON dengan pesan error
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Mengekspor data penjualan ke format PDF
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function export_pdf()
    {
        // Mengambil data detail penjualan dengan relasi penjualan dan barang
        $penjualan = DetailPenjualanModel::select('penjualan_id', 'barang_id', 'harga', 'jumlah')
            ->orderBy('penjualan_id')
            ->with('penjualan', 'barang')
            ->get();

        // Memuat view penjualan.pdf untuk PDF
        $pdf = Pdf::loadView('penjualan.pdf', ['penjualan' => $penjualan]);
        // Mengatur ukuran kertas A4 portrait
        $pdf->setPaper('a4', 'portrait');
        // Mengaktifkan opsi remote untuk mendukung file eksternal
        $pdf->setOption("isRemoteEnbled", true);
        // Render PDF
        $pdf->render();

        // Mengembalikan stream PDF dengan nama file berdasarkan tanggal
        return $pdf->stream('Data Barang'.date('Y-m-d').'.pdf');
    }
}
