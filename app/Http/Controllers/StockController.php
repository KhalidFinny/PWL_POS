<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockModel;
use App\Models\BarangModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Menampilkan halaman utama daftar stok barang
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Membuat objek breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Stok Barang',
            'list' => ['Home', 'Data Stok']
        ];

        // Membuat objek page untuk judul halaman
        $page = (object) [
            'title' => 'Daftar Stok Barang yang terdaftar dalam sistem'
        ];

        // Menentukan menu aktif
        $activeMenu = 'stok';

        // Inisialisasi variabel stok sebagai null (untuk form tambah, bukan edit)
        $stok = null; // or existing stock object for editing
        // Mengambil semua data barang
        $barang = BarangModel::all();
        // Mengambil semua data supplier
        $supplier = SupplierModel::all();
        // Mengambil data user dengan level_id <= 3 (misalnya hanya staff yang boleh input)
        $user = UserModel::where('level_id', '<=', 3)->get();

        // Mengembalikan view stok.index dengan data yang diperlukan
        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'barang' => $barang,
            'supplier' => $supplier,
            'user' => $user,
            'stok' => $stok
        ]);
    }

    /**
     * Mengambil data stok untuk ditampilkan dalam DataTables
     * @param Request $request Data filter dari request
     * @return \Yajra\DataTables\Facades\DataTables
     */
    public function list(Request $request)
    {
        // Mengambil data stok dengan relasi barang, supplier, dan user
        $stok = StockModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stik_jumlah')
            ->with(['barang', 'supplier', 'user']);

        // Mengambil filter barang dan supplier dari request
        $filter_barang = $request->input('filter_barang');
        $filter_supplier = $request->input('filter_supplier');
        // Memfilter berdasarkan barang_id jika filter_barang tidak kosong
        if (!empty($filter_barang)) {
            $stok->where('barang_id', $filter_barang);
        }
        // Memfilter berdasarkan supplier_id jika filter_supplier tidak kosong
        if (!empty($filter_supplier)) {
            $stok->where('supplier_id', $filter_supplier);
        }

        // Mengembalikan data dalam format DataTables
        return DataTables::of($stok)
            ->addIndexColumn() // Menambahkan kolom nomor urut
            ->addColumn('barang_id', function ($stock) {
                // Menampilkan nama barang, jika tidak ada tampilkan '-'
                return $stock->barang ? $stock->barang->barang_nama : '-';
            })
            ->addColumn('supplier_id', function ($stock) {
                // Menampilkan nama supplier, jika tidak ada tampilkan '-'
                return $stock->supplier ? $stock->supplier->supplier_nama : '-';
            })
            ->addColumn('user_id', function ($stock) {
                // Menampilkan nama user, jika tidak ada tampilkan '-'
                return $stock->user ? $stock->user->nama : '-';
            })
            ->addColumn('aksi', function ($stock) {
                // Membersihkan stok_id dari karakter non-numerik
                $id = preg_replace('/[^0-9]/', '', $stock->stok_id); // Clean ID
                // Menambahkan tombol untuk membuka modal tambah stok
                return '<button onclick="openIncrementModal(\''.$id.'\', \''.$stock->stok_jumlah.'\')" class="btn btn-sm btn-success">Tambah Stok</button>';
            })
            ->rawColumns(['aksi']) // Mengizinkan HTML pada kolom aksi
            ->make(true);
    }

    /**
     * Menambah jumlah stok barang
     * @param Request $request Data input stok_id dan jumlah
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment(Request $request)
    {
        // Validasi input
        $request->validate([
            'stok_id' => 'required|integer|exists:t_stok,stok_id', // Stok ID wajib ada di tabel stok
            'stok_jumlah' => 'required|integer|min:1', // Jumlah stok wajib integer dan minimal 1
        ]);

        // Mengambil data stok berdasarkan stok_id
        $stok = StockModel::find($request->stok_id);
        if (!$stok) {
            // Mengembalikan respons JSON jika stok tidak ditemukan
            return response()->json([
                'status' => false,
                'message' => 'Stock not found',
            ], 404);
        }

        // Menambah jumlah stok
        $stok->stok_jumlah += $request->stok_jumlah;
        $stok->save();

        // Mengembalikan respons JSON sukses
        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah stok',
        ], 200);
    }

    /**
     * Menampilkan konfirmasi hapus stok melalui AJAX
     * @param string $id ID stok
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm_ajax(string $id)
    {
        // Mengambil data stok berdasarkan ID
        $stok = StockModel::find($id);
        if (!$stok) {
            // Mengembalikan respons JSON jika stok tidak ditemukan
            return response()->json([
                'status' => false,
                'message' => 'Stok tidak ditemukan'
            ], 404);
        }

        // Mengembalikan respons JSON dengan data untuk konfirmasi hapus
        return response()->json([
            'status' => true,
            'message' => 'Konfirmasi hapus stok',
            'data' => [
                'stok_id' => $stok->stok_id,
                'delete_url' => url('/stok/' . $stok->stok_id)
            ]
        ], 200);
    }

    /**
     * Menghapus data stok
     * @param Request $request Request untuk memeriksa AJAX
     * @param string $id ID stok
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $id)
    {
        // Memastikan request adalah AJAX atau menginginkan JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Mengambil data stok berdasarkan ID
            $stok = StockModel::find($id);
            if ($stok) {
                // Memeriksa apakah stok terkait dengan transaksi lain
                $hasReferences = DB::table('t_stok')->where('stok_id', $id)->exists();
                if ($hasReferences) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data stok tidak dapat dihapus karena terkait dengan transaksi'
                    ], 422);
                }
                try {
                    // Menghapus stok (soft delete)
                    $stok->delete();
                    // Mengembalikan respons JSON sukses
                    return response()->json([
                        'status' => true,
                        'message' => 'Data stok berhasil dihapus'
                    ], 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Log error jika gagal menghapus
                    Log::error('Failed to delete stock ID ' . $id . ': ' . $e->getMessage());
                    // Mengembalikan respons JSON dengan pesan error
                    return response()->json([
                        'status' => false,
                        'message' => 'Data stok gagal dihapus karena masih terkait dengan data lain'
                    ], 422);
                }
            }
            // Mengembalikan respons JSON jika stok tidak ditemukan
            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
            ], 404);
        }
        // Redirect ke halaman utama jika bukan request AJAX
        return redirect('/');
    }

    /**
     * Menampilkan halaman import data stok
     * @return \Illuminate\View\View
     */
    public function import()
    {
        // Mengembalikan view stok.import
        return view('stok.import');
    }

    /**
     * Mengimpor data stok dari file Excel melalui AJAX
     * @param Request $request File Excel dari input
     * @return \Illuminate\Http\JsonResponse
     */
    public function import_ajax(Request $request)
    {
        // Memastikan request adalah AJAX atau menginginkan JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi file yang diunggah
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024'], // File wajib xlsx, maks 1MB
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // Mengembalikan respons JSON jika validasi gagal
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()->messages()
                ], 422);
            }

            // Mengambil file Excel
            $file = $request->file('file_stok');
            // Membaca file Excel
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            // Memeriksa apakah data memiliki lebih dari 1 baris (header + data)
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    // Lewati baris header (baris 1)
                    if ($baris > 1) {
                        $record = [
                            'barang_id' => $value['A'], // Kolom A: barang_id
                            'supplier_id' => $value['B'], // Kolom B: supplier_id
                            'user_id' => $value['C'], // Kolom C: user_id
                            'stok_tanggal' => date('Y-m-d', strtotime($value['D'])), // Kolom D: tanggal stok
                            'stok_jumlah' => $value['E'], // Kolom E: jumlah stok
                            'created_at' => now(),
                        ];
                        // Memeriksa apakah stok dengan kombinasi yang sama sudah ada
                        $existing = StockModel::where([
                            'barang_id' => $record['barang_id'],
                            'supplier_id' => $record['supplier_id'],
                            'user_id' => $record['user_id'],
                            'stok_tanggal' => $record['stok_tanggal']
                        ])->first();
                        if ($existing) {
                            // Jika ada, tambahkan jumlah stok
                            $existing->stok_jumlah += $record['stok_jumlah'];
                            $existing->save();
                        } else {
                            // Jika tidak ada, buat stok baru
                            StockModel::create($record);
                        }
                    }
                }
                // Mengembalikan respons JSON sukses
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diimport'
                ], 200);
            }
            // Mengembalikan respons JSON jika tidak ada data
            return response()->json([
                'status' => false,
                'message' => 'Data tidak bisa diimport'
            ], 422);
        }
        // Redirect ke halaman utama jika bukan request AJAX
        return redirect('/');
    }

    /**
     * Mengekspor data stok ke format Excel
     * @return void
     */
    public function export_excel()
    {
        // Mengambil data stok dengan relasi barang, supplier, dan user
        $stok = StockModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with(['barang', 'supplier', 'user'])
            ->orderBy('stok_tanggal')
            ->get();

        // Membuat spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Mengatur header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Nama Supplier');
        $sheet->setCellValue('D1', 'Nama User');
        $sheet->setCellValue('E1', 'Tanggal Stok');
        $sheet->setCellValue('F1', 'Jumlah Stok');

        // Membuat header tebal
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $no = 1;
        $baris = 2;
        // Mengisi data stok ke spreadsheet
        foreach ($stok as $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->barang ? $value->barang->barang_nama : 'N/A');
            $sheet->setCellValue('C' . $baris, $value->supplier ? $value->supplier->supplier_nama : 'N/A');
            $sheet->setCellValue('D' . $baris, $value->user ? $value->user->nama : 'N/A');
            $sheet->setCellValue('E' . $baris, date('d-m-Y', strtotime($value->stok_tanggal)));
            $sheet->setCellValue('F' . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }
        // Mengatur lebar kolom otomatis
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        // Mengatur judul sheet
        $sheet->setTitle('Data Stok');
        // Membuat writer untuk format Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d') . '.xlsx';

        // Mengatur header untuk download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // Mengirim file Excel ke output
        $writer->save('php://output');
        exit;
    }

    /**
     * Mengambil daftar stok yang telah dihapus (soft delete) untuk DataTables
     * @param Request $request Request untuk memeriksa filter jika ada
     * @return \Yajra\DataTables\Facades\DataTables
     */
    public function listDeleted(Request $request)
    {
        // Mengambil data stok yang telah dihapus (soft delete) dengan relasi
        $deletedStocks = StockModel::onlyTrashed()
            ->with(['barang', 'supplier', 'user'])
            ->select('t_stok.*');

        // Mengembalikan data dalam format DataTables
        return DataTables::of($deletedStocks)
            ->addIndexColumn() // Menambahkan kolom nomor urut
            ->addColumn('barang_nama', function ($stock) {
                // Menampilkan nama barang, jika tidak ada tampilkan '-'
                return $stock->barang->barang_nama ?? '-';
            })
            ->addColumn('supplier_nama', function ($stock) {
                // Menampilkan nama supplier, jika tidak ada tampilkan '-'
                return $stock->supplier->supplier_nama ?? '-';
            })
            ->addColumn('user_nama', function ($stock) {
                // Menampilkan nama user, jika tidak ada tampilkan '-'
                return $stock->user->nama ?? '-';
            })
            ->addColumn('aksi', function ($stock) {
                // Menambahkan tombol untuk restore stok
                return '<button onclick="restoreStock('.$stock->stok_id.')" class="btn btn-sm btn-success">
                        <i class="fas fa-redo"></i> Restok
                    </button>';
            })
            ->rawColumns(['aksi']) // Mengizinkan HTML pada kolom aksi
            ->make(true);
    }

    /**
     * Menangani restok barang (restore stok yang dihapus atau tambah jumlah)
     * @param Request $request Data input stok_id dan jumlah
     * @return \Illuminate\Http\JsonResponse
     */
    public function restock(Request $request)
    {
        // Validasi input
        $request->validate([
            'stok_id' => 'required|integer|exists:t_stok,stok_id', // Stok ID wajib ada di tabel stok
            'jumlah' => 'required|integer|min:1' // Jumlah wajib integer dan minimal 1
        ]);

        // Memulai transaksi database
        DB::beginTransaction();
        try {
            // Mengambil stok, termasuk yang telah dihapus (soft delete)
            $stock = StockModel::withTrashed()->findOrFail($request->stok_id);

            if ($stock->trashed()) {
                // Jika stok telah dihapus, restore dan set jumlah baru
                $stock->restore();
                $stock->stok_jumlah = $request->jumlah;
            } else {
                // Jika stok masih ada, tambahkan jumlah
                $stock->increment('stok_jumlah', $request->jumlah);
            }

            // Simpan perubahan
            $stock->save();

            // Commit transaksi
            DB::commit();

            // Mengembalikan respons JSON sukses
            return response()->json([
                'status' => true,
                'message' => 'Stok berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            // Mengembalikan respons JSON dengan pesan error
            return response()->json([
                'status' => false,
                'message' => 'Gagal restok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengekspor data stok ke format PDF
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function export_pdf()
    {
        // Mengambil data stok dengan relasi barang, supplier, dan user
        $stok = StockModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with(['barang', 'supplier', 'user'])
            ->orderBy('stok_tanggal')
            ->get();

        // Memuat view stok.export_pdf untuk PDF
        $pdf = Pdf::loadView('stock.export_pdf', ['stok' => $stok]);
        // Mengatur ukuran kertas A4 landscape
        $pdf->setPaper('a4', 'landscape');
        // Mengaktifkan opsi remote untuk mendukung file eksternal
        $pdf->setOption("isRemoteEnabled", true);
        // Render PDF
        $pdf->render();

        // Mengembalikan stream PDF dengan nama file berdasarkan tanggal
        return $pdf->stream('Data Stok ' . date('Y-m-d') . '.pdf');
    }
}
