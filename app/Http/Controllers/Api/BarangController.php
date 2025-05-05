<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {
        return BarangModel::with('kategori')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'supplier_id' => 'nullable|exists:m_supplier,supplier_id',
            'brang_kode' => 'required|string|max:10|unique:m_barang,brang_kode',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('barang'), $image);

        $barang = BarangModel::create([
            'kategori_id' => $request->kategori_id,
            'supplier_id' => $request->supplier_id,
            'brang_kode' => $request->brang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'image' => $image,
        ]);

        if ($barang) {
            return response()->json([
                'success' => true,
                'barang' => $barang->load('kategori'),
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }

    public function show(BarangModel $barang)
    {
        return $barang->load('kategori');
    }

    public function update(Request $request, BarangModel $barang)
    {
        $barang->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Barang berhasil diupdate',
            'data' => $barang,
        ], 200);
    }

    public function destroy(BarangModel $barang)
    {
        try {
            $barang->delete();
            return response()->json([
                'status' => true,
                'message' => 'Barang berhasil dihapus',
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menghapus barang karena terkait dengan data lain (misal: stok atau transaksi).',
                ], 409);
            }
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus barang.',
            ], 500);
        }
    }
}
