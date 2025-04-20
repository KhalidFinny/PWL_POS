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
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Barang',
            'list' => ['Home', 'Data Barang']
        ];

        $page = (object) [
            'title' => 'Daftar Barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang';
        $kategori = KategoriModel::all();
        $supplier = SupplierModel::all();

        return view('barang.index', compact('kategori', 'breadcrumb', 'page', 'activeMenu', 'supplier'));
    }

    public function list(Request $request)
    {
        $barang = BarangModel::select('barang_id', 'kategori_id', 'brang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'supplier_id')
            ->with(['kategori', 'supplier']);

        if ($request->has('filter_kategori') && $request->filter_kategori != '') {
            $barang->where('kategori_id', $request->filter_kategori);
        }

        return DataTables::of($barang)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->editColumn('kategori_id', function ($barang) {
                return $barang->kategori ? $barang->kategori->kategori_nama : 'N/A';
            })
            ->editColumn('supplier_id', function ($barang) {
                return $barang->supplier ? $barang->supplier->supplier_nama : 'N/A';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::all();
        $supplier = SupplierModel::all();
        return view('barang.create_ajax', compact('kategori', 'supplier'));
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id',
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
            'brang_kode' => 'required|string|min:3|max:20|unique:m_barang,brang_kode',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0|gte:harga_beli'
        ], [
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            BarangModel::create([
                'kategori_id' => $request->kategori_id,
                'supplier_id' => $request->supplier_id,
                'brang_kode' => $request->brang_kode,
                'barang_nama' => $request->barang_nama,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show_ajax($id)
    {
        $barang = BarangModel::with(['kategori', 'supplier'])->find($id);
        return view('barang.show_ajax', compact('barang'));
    }

    public function edit_ajax($id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all();
        $supplier = SupplierModel::all();
        return view('barang.edit_ajax', compact('barang', 'kategori', 'supplier'));
    }

    public function update_ajax(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|integer|exists:m_kategori,kategori_id',
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
            'brang_kode' => 'required|string|min:3|max:20|unique:m_barang,brang_kode,'.$id.',barang_id',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0|gte:harga_beli'
        ], [
            'harga_jual.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $barang = BarangModel::find($id);
            $barang->update([
                'kategori_id' => $request->kategori_id,
                'supplier_id' => $request->supplier_id,
                'brang_kode' => $request->brang_kode,
                'barang_nama' => $request->barang_nama,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui',
                'redirect' => url('/')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirm_ajax($id)
    {
        $barang = BarangModel::find($id);
        return view('barang.confirm_ajax', compact('barang'));
    }

    public function delete_ajax($id)
{
    try {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Data barang tidak ditemukan'
            ], 404);
        }

        // Wrap deletion in a transaction to ensure data integrity
        DB::beginTransaction();

        // Option 1: Force delete related stok records (permanently remove)
        StockModel::where('barang_id', $id)->forceDelete();

        // Option 2: Soft delete related stok records (mark as deleted)
        // StockModel::where('barang_id', $id)->delete();

        // Delete the barang record
        $barang->delete();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data barang dan stok terkait berhasil dihapus'
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Gagal menghapus data: ' . $e->getMessage()
        ], 500);
    }
}

    public function import()
    {
        return view('barang.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024'],
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()->messages()
                ]);
            }
            $file = $request->file('file_barang');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert[] = [
                            'kategori_id' => $value['A'],
                            'brang_kode' => $value['B'],
                            'barang_nama' => $value['C'],
                            'harga_beli' => $value['D'],
                            'harga_jual' => $value['E'],
                            'created_at' => now(),
                        ];
                    }
                }
                if (count($insert) > 0) {
                    BarangModel::insertOrIgnore($insert);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak bisa diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $barang = BarangModel::select('barang_id', 'brang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Harga Beli');
        $sheet->setCellValue('E1', 'Harga Jual');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $no = 1;
        $baris = 2;
        foreach ($barang as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->brang_kode);
            $sheet->setCellValue('C' . $baris, $value->barang_nama);
            $sheet->setCellValue('D' . $baris, $value->harga_beli);
            $sheet->setCellValue('E' . $baris, $value->harga_jual);
            $baris++;
            $no++;
        }
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setTitle('Data Barang');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Barang ' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $barang = BarangModel::select('barang_id', 'brang_kode', 'barang_nama', 'harga_beli','harga_jual', 'kategori_id')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();

        $pdf = Pdf::loadView('barang.export_pdf', ['barang' => $barang]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnbled", true);
        $pdf->render();

        return $pdf->stream('Data Barang'.date('Y-m-d').'.pdf');
    }
}
