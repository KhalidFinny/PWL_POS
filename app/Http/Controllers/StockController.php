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
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Stok Barang',
            'list' => ['Home', 'Data Stok']
        ];

        $page = (object) [
            'title' => 'Daftar Stok Barang yang terdaftar dalam sistem'
        ];
        $activeMenu = 'stok';
        $stok = null; // or existing stock object for editing
    $barang = BarangModel::all();
    $supplier = SupplierModel::all();
    $user = UserModel::where('level_id', '<=', 3)->get(); // assuming only staff can input

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

    public function list(Request $request)
    {
        $stok = StockModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with(['barang', 'supplier', 'user']);

        $filter_barang = $request->input('filter_barang');
        $filter_supplier = $request->input('filter_supplier');
        if (!empty($filter_barang)) {
            $stok->where('barang_id', $filter_barang);
        }
        if (!empty($filter_supplier)) {
            $stok->where('supplier_id', $filter_supplier);
        }
    return DataTables::of($stok)
    ->addIndexColumn()
    ->addColumn('barang_id', function ($stock) {
        return $stock->barang ? $stock->barang->barang_nama : '-';
    })
    ->addColumn('supplier_id', function ($stock) {
        return $stock->supplier ? $stock->supplier->supplier_nama : '-';
    })
    ->addColumn('user_id', function ($stock) {
        return $stock->user ? $stock->user->nama : '-';
    })
    ->addColumn('aksi', function ($stock) {
        $id = preg_replace('/[^0-9]/', '', $stock->stok_id); // Clean ID
        return '<button onclick="openIncrementModal(\''.$id.'\', \''.$stock->stok_jumlah.'\')" class="btn btn-sm btn-success">Tambah Stok</button>';
    })
    ->rawColumns(['aksi'])
    ->make(true);
}



    public function increment(Request $request)
    {
        $request->validate([
            'stok_id' => 'required|integer|exists:t_stok,stok_id',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        $stok = StockModel::find($request->stok_id);
        if (!$stok) {
            return response()->json([
                'status' => false,
                'message' => 'Stock not found',
            ], 404);
        }

        $stok->stok_jumlah += $request->stok_jumlah;
        $stok->save();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah stok',
        ]);
    }

    public function confirm_ajax(string $id)
    {
        $stok = StockModel::find($id);
        if (!$stok) {
            return response()->json([
                'status' => false,
                'message' => 'Stok tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Konfirmasi hapus stok',
            'data' => [
                'stok_id' => $stok->stok_id,
                'delete_url' => url('/stok/' . $stok->stok_id)
            ]
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StockModel::find($id);
            if ($stok) {
                // Example: Check if stock is referenced in another table (e.g., transactions)
                $hasReferences = DB::table('t_stok')->where('stok_id', $id)->exists();
                if ($hasReferences) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data stok tidak dapat dihapus karena terkait dengan transaksi'
                    ], 422);
                }
                try {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data stok berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::error('Failed to delete stock ID ' . $id . ': ' . $e->getMessage());
                    return response()->json([
                        'status' => false,
                        'message' => 'Data stok gagal dihapus karena masih terkait dengan data lain'
                    ], 422);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
            ], 404);
        }
        return redirect('/');
    }

    public function import()
    {
        return view('stok.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024'],
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()->messages()
                ]);
            }
            $file = $request->file('file_stok');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $record = [
                            'barang_id' => $value['A'],
                            'supplier_id' => $value['B'],
                            'user_id' => $value['C'],
                            'stok_tanggal' => date('Y-m-d', strtotime($value['D'])),
                            'stok_jumlah' => $value['E'],
                            'created_at' => now(),
                        ];
                        // Check for existing stock record
                        $existing = StockModel::where([
                            'barang_id' => $record['barang_id'],
                            'supplier_id' => $record['supplier_id'],
                            'user_id' => $record['user_id'],
                            'stok_tanggal' => $record['stok_tanggal']
                        ])->first();
                        if ($existing) {
                            $existing->stok_jumlah += $record['stok_jumlah'];
                            $existing->save();
                        } else {
                            StockModel::create($record);
                        }
                    }
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diimport'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Data tidak bisa diimport'
            ]);
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $stok = StockModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with(['barang', 'supplier', 'user'])
            ->orderBy('stok_tanggal')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Nama Supplier');
        $sheet->setCellValue('D1', 'Nama User');
        $sheet->setCellValue('E1', 'Tanggal Stok');
        $sheet->setCellValue('F1', 'Jumlah Stok');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $no = 1;
        $baris = 2;
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
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setTitle('Data Stok');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function listDeleted(Request $request)
{
    $deletedStocks = StockModel::onlyTrashed()
        ->with(['barang', 'supplier', 'user'])
        ->select('t_stok.*');

    return DataTables::of($deletedStocks)
        ->addIndexColumn()
        ->addColumn('barang_nama', function ($stock) {
            return $stock->barang->barang_nama ?? '-';
        })
        ->addColumn('supplier_nama', function ($stock) {
            return $stock->supplier->supplier_nama ?? '-';
        })
        ->addColumn('user_nama', function ($stock) {
            return $stock->user->nama ?? '-';
        })
        ->addColumn('aksi', function ($stock) {
            return '<button onclick="restoreStock('.$stock->stok_id.')" class="btn btn-sm btn-success">
                    <i class="fas fa-redo"></i> Restok
                </button>';
        })
        ->rawColumns(['aksi'])
        ->make(true);
}
// Tambahkan method baru untuk menangani restok
public function restock(Request $request)
{
    $request->validate([
        'stok_id' => 'required|integer|exists:t_stok,stok_id',
        'jumlah' => 'required|integer|min:1'
    ]);

    DB::beginTransaction();
    try {
        $stock = StockModel::withTrashed()->findOrFail($request->stok_id);

        if ($stock->trashed()) {
            $stock->restore();
            $stock->stok_jumlah = $request->jumlah;
        } else {
            $stock->increment('stok_jumlah', $request->jumlah);
        }

        $stock->save();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Stok berhasil ditambahkan'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Gagal restok: ' . $e->getMessage()
        ], 500);
    }
}

    public function export_pdf()
    {
        $stok = StockModel::select('stok_id', 'barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->with(['barang', 'supplier', 'user'])
            ->orderBy('stok_tanggal')
            ->get();

        $pdf = Pdf::loadView('stock.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Stok ' . date('Y-m-d') . '.pdf');
    }
}
