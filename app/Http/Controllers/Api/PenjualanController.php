<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\StockModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])->get();
        return response()->json([
            'status' => true,
            'data' => $penjualan,
        ], 200);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'pembeli' => 'required|string|max:100',
        'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode',
        'stok_id' => 'required|exists:t_stok,stok_id',
        'jumlah' => 'required|integer|min:1',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    DB::beginTransaction();
    try {
        // Debug image upload
        if (!$request->hasFile('image')) {
            throw new \Exception('No image file uploaded');
        }
        $image = $request->file('image');
        $imageName = $image->hashName();
        Log::info('Image Name: ' . $imageName); // Log the filename
        $image->move(public_path('penjualan'), $imageName);
        Log::info('Image moved to: ' . public_path('penjualan') . '/' . $imageName); // Verify destination

        // Create penjualan
        $penjualan = PenjualanModel::create([
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => now(),
            'image' => $imageName,
        ]);

        // Process details
        $stok = StockModel::find($request->stok_id);
        if ($stok->stok_jumlah < $request->jumlah) {
            throw new \Exception("Stok tidak mencukupi untuk barang: " . $stok->barang->barang_nama);
        }

        DetailPenjualanModel::create([
            'penjualan_id' => $penjualan->penjualan_id,
            'barang_id' => $stok->barang_id,
            'harga' => $stok->barang->harga_jual,
            'jumlah' => $request->jumlah,
        ]);

        $stok->decrement('stok_jumlah', $request->jumlah);
        if ($stok->stok_jumlah <= 0) {
            $stok->delete();
        }

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Penjualan berhasil ditambahkan',
            'data' => $penjualan->load(['user', 'details.barang']),
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Gagal menambahkan penjualan: ' . $e->getMessage(),
        ], 422);
    }
}

    public function show(PenjualanModel $penjualan)
    {
        return response()->json([
            'status' => true,
            'data' => $penjualan->load(['user', 'details.barang']),
        ], 200);
    }

    public function update(Request $request, PenjualanModel $penjualan)
    {
        $validator = Validator::make($request->all(), [
            'pembeli' => 'sometimes|string|max:100',
            'penjualan_kode' => 'sometimes|string|unique:t_penjualan,penjualan_kode,' . $penjualan->penjualan_id . ',penjualan_id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($penjualan->image) {
                    unlink(public_path('penjualan/' . $penjualan->image));
                }
                $imageName = $request->file('image')->hashName();
                $request->file('image')->move(public_path('penjualan'), $imageName);
                $penjualan->image = $imageName;
            }

            $penjualan->update($request->except('image'));
            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil diupdate',
                'data' => $penjualan->load(['user', 'details.barang']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate penjualan: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(PenjualanModel $penjualan)
    {
        DB::beginTransaction();
        try {
            // Restore stock
            foreach ($penjualan->details as $detail) {
                $stok = StockModel::where('barang_id', $detail->barang_id)->first();
                if ($stok) {
                    $stok->increment('stok_jumlah', $detail->jumlah);
                }
            }

            // Delete details
            DetailPenjualanModel::where('penjualan_id', $penjualan->penjualan_id)->delete();

            // Delete image
            if ($penjualan->image) {
                unlink(public_path('penjualan/' . $penjualan->image));
            }

            // Delete penjualan
            $penjualan->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus penjualan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
