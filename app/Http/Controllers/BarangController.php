<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\StockModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman utama daftar barang
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Membuat objek breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Barang',
            'list' => ['Home', 'Data Barang']
        ];

        // Membuat objek page untuk judul halaman
        $page = (object) [
            'title' => 'Daftar Barang yang terdaftar dalam sistem'
        ];

        // Menentukan menu aktif
        $activeMenu = 'barang';

        // Mengambil semua data kategori dan supplier
        $kategori = KategoriModel::all();
        $supplier = SupplierModel::all();

        // Mengembalikan view barang.index dengan data yang diperlukan
        return view('barang.index', compact('kategori', 'breadcrumb', 'page', 'activeMenu', 'supplier'));
    }

    /**
     * Mengambil data barang untuk ditampilkan dalam DataTables
     * @param Request $request Data filter dari request
     * @return \Yajra\DataTables\Facades\DataTables
     */
    public function list(Request $request)
    {
        // Mengambil data barang dengan relasi kategori dan supplier
        $barang = BarangModel::select('barang_id', 'kategori_id', 'brang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'supplier_id')
            ->with(['kategori', 'supplier']);

        // Memfilter berdasarkan kategori jika filter_kategori ada dan tidak kosong
        if ($request->has('filter_kategori') && $request->filter_kategori != '') {
            $barang->where('kategori_id', $request->filter_kategori);
        }

        // Mengembalikan data dalam format DataTables
        return DataTables::of($barang)
            ->addIndexColumn() // Menambahkan kolom nomor urut
            ->addColumn('aksi', function ($barang) {
                // Menambahkan tombol aksi untuk detail, edit, dan hapus
                $btn = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->editColumn('kategori_id', function ($barang) {
                // Menampilkan nama kategori, jika tidak ada tampilkan 'N/A'
                return $barang->kategori ? $barang->kategori->kategori_nama : 'N/A';
            })
            ->editColumn('supplier_id', function ($barang) {
                // Menampilkan nama supplier, jika tidak ada tampilkan 'N/A'
                return $barang->supplier ? $barang->supplier->supplier_nama : 'N/A';
            })
            ->rawColumns(['aksi']) // Mengizinkan HTML pada kolom aksi
            ->make(true);
    }

    /**
     * Menampilkan form tambah barang melalui AJAX
     * @return \Illuminate\View\View
     */
    public function create_ajax()
    {
        // Mengambil semua data kategori dan supplier untuk form
        $kategori = KategoriModel::all();
        $supplier = SupplierModel::all();
        // Mengembalikan view barang.create_ajax
        return view('barang.create_ajax', compact('kategori', 'supplier'));
    }

    /**
     * Menyimpan data barang baru melalui AJAX
     * @param Request $request Data input dari form
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_ajax(Request $request)
    {
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id', // Kategori wajib ada di tabel kategori
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id', // Supplier wajib ada di tabel supplier
            'brang_kode' => 'required|string|min:3|max:20|unique:m_barang,brang_kode', // Kode barang wajib unik
            'barang_nama' => 'required|string|max:100', // Nama barang wajib diisi, maks 100 karakter
            'harga_beli' => 'required|numeric|min:0', // Harga beli wajib angka, minimal 0
            'harga_jual' => 'required|numeric|min:0|gte:harga_beli' // Harga jual wajib >= harga beli
        ], [
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli'
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Menyimpan data barang baru ke database
            BarangModel::create([
                'kategori_id' => $request->kategori_id,
                'supplier_id' => $request->supplier_id,
                'brang_kode' => $request->brang_kode,
                'barang_nama' => $request->barang_nama,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual
            ]);

            // Mengembalikan respons JSON sukses
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            // Mengembalikan respons JSON dengan pesan error jika gagal
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail barang melalui AJAX
     * @param int $id ID barang
     * @return \Illuminate\View\View
     */
    public function show_ajax($id)
    {
        // Mengambil data barang dengan relasi kategori dan supplier
        $barang = BarangModel::with(['kategori', 'supplier'])->find($id);
        // Mengembalikan view barang.show_ajax
        return view('barang.show_ajax', compact('barang'));
    }

    /**
     * Menampilkan form edit barang melalui AJAX
     * @param int $id ID barang
     * @return \Illuminate\View\View
     */
    public function edit_ajax($id)
    {
        // Mengambil data barang berdasarkan ID
        $barang = BarangModel::find($id);
        // Mengambil semua data kategori dan supplier untuk form
        $kategori = KategoriModel::all();
        $supplier = SupplierModel::all();
        // Mengembalikan view barang.edit_ajax
        return view('barang.edit_ajax', compact('barang', 'kategori', 'supplier'));
    }

    /**
     * Memperbarui data barang melalui AJAX
     * @param Request $request Data input dari form
     * @param int $id ID barang
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_ajax(Request $request, $id)
    {
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id', // Kategori wajib ada
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id', // Supplier wajib ada
            'brang_kode' => 'required|string|min:3|max:20|unique:m_barang,brang_kode,'.$id.',barang_id', // Kode barang unik kecuali untuk ID ini
            'barang_nama' => 'required|string|max:100', // Nama barang wajib diisi
            'harga_beli' => 'required|numeric|min:0', // Harga beli wajib angka
            'harga_jual' => 'required|numeric|min:0|gte:harga_beli' // Harga jual wajib >= harga beli
        ], [
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli'
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Mengambil data barang berdasarkan ID
            $barang = BarangModel::find($id);
            // Memperbarui data barang
            $barang->update([
                'kategori_id' => $request->kategori_id,
                'supplier_id' => $request->supplier_id,
                'brang_kode' => $request->brang_kode,
                'barang_nama' => $request->barang_nama,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual
            ]);

            // Mengembalikan respons JSON sukses
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui',
                'redirect' => url('/')
            ], 200);

        } catch (\Exception $e) {
            // Mengembalikan respons JSON dengan pesan error jika gagal
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan konfirmasi hapus barang melalui AJAX
     * @param int $id ID barang
     * @return \Illuminate\View\View
     */
    public function confirm_ajax($id)
    {
        // Mengambil data barang berdasarkan ID
        $barang = BarangModel::find($id);
        // Mengembalikan view barang.confirm_ajax
        return view('barang.confirm_ajax', compact('barang'));
    }

    /**
     * Menghapus data barang dan stok terkait melalui AJAX
     * @param int $id ID barang
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_ajax($id)
    {
        try {
            // Mengambil data barang berdasarkan ID
            $barang = BarangModel::find($id);

            // Memeriksa apakah barang ditemukan
            if (!$barang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data barang tidak ditemukan'
                ], 404);
            }

            // Memulai transaksi database
            DB::beginTransaction();
            // Menghapus stok terkait barang (hard delete)
            StockModel::where('barang_id', $id)->forceDelete();
            // Menghapus barang (soft delete)
            $barang->delete();
            // Commit transaksi
            DB::commit();

            // Mengembalikan respons JSON sukses
            return response()->json([
                'status' => true,
                'message' => 'Data barang dan stok terkait berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            // Mengembalikan respons JSON dengan pesan error
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman import data barang
     * @return \Illuminate\View\View
     */
    public function import()
    {
        // Mengembalikan view barang.import
        return view('barang.import');
    }

    /**
     * Mengimpor data barang dari file Excel melalui AJAX
     * @param Request $request File Excel dari input
     * @return \Illuminate\Http\JsonResponse
     */
    public function import_ajax(Request $request)
    {
        // Memastikan request adalah AJAX atau menginginkan JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi file yang diunggah
            $rules = [
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024'], // File wajib xlsx, maks 1MB
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()->messages()
                ], 422);
            }

            // Mengambil file Excel
            $file = $request->file('file_barang');
            // Membaca file Excel
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            // Memeriksa apakah data memiliki lebih dari 1 baris (header + data)
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    // Lewati baris header (baris 1)
                    if ($baris > 1) {
                        $insert[] = [
                            'kategori_id' => $value['A'], // Kolom A: kategori_id
                            'brang_kode' => $value['B'], // Kolom B: kode barang
                            'barang_nama' => $value['C'], // Kolom C: nama barang
                            'harga_beli' => $value['D'], // Kolom D: harga beli
                            'harga_jual' => $value['E'], // Kolom E: harga jual
                            'created_at' => now(),
                        ];
                    }
                }
                // Menyisipkan data ke database, abaikan jika ada duplikat
                if (count($insert) > 0) {
                    BarangModel::insertOrIgnore($insert);
                }
                // Mengembalikan respons JSON sukses
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ], 200);
            } else {
                // Mengembalikan respons JSON jika tidak ada data
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak bisa diimport'
                ], 422);
            }
        }
        // Redirect ke halaman utama jika bukan request AJAX
        return redirect('/');
    }

    /**
     * Mengekspor data barang ke format Excel
     * @return void
     */
    public function export_excel()
    {
        // Mengambil data barang dengan relasi kategori, diurutkan berdasarkan kategori_id
        $barang = BarangModel::select('barang_id', 'brang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();

        // Membuat spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Mengatur header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Harga Beli');
        $sheet->setCellValue('E1', 'Harga Jual');

        // Membuat header tebal
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $no = 1;
        $baris = 2;
        // Mengisi data barang ke spreadsheet
        foreach ($barang as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->brang_kode);
            $sheet->setCellValue('C' . $baris, $value->barang_nama);
            $sheet->setCellValue('D' . $baris, $value->harga_beli);
            $sheet->setCellValue('E' . $baris, $value->harga_jual);
            $baris++;
            $no++;
        }
        // Mengatur lebar kolom otomatis
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        // Mengatur judul sheet
        $sheet->setTitle('Data Barang');
        // Membuat writer untuk format Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Barang ' . date('Y-m-d') . '.xlsx';

        // Mengatur header untuk download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Mengirim file Excel ke output
        $writer->save('php://output');
        exit;
    }

    /**
     * Mengekspor data barang ke format PDF
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function export_pdf()
    {
        // Mengambil data barang dengan relasi kategori, diurutkan berdasarkan kategori_id
        $barang = BarangModel::select('barang_id', 'brang_kode', 'barang_nama', 'harga_beli','harga_jual', 'kategori_id')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();

        // Memuat view barang.export_pdf untuk PDF
        $pdf = Pdf::loadView('barang.export_pdf', ['barang' => $barang]);
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
