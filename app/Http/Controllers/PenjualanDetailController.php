<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function showInvoice($id)
    {
        $penjualan = PenjualanModel::with(['details.barang', 'user'])->findOrFail($id);

        return view('penjualan.invoice', compact('penjualan'));
    }
}
