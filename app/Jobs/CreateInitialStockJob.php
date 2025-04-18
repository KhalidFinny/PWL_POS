<?php

namespace App\Jobs;

use App\Models\BarangModel;
use App\Models\StockModel;
use App\Models\UserModel;
use App\Models\SupplierModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateInitialStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $barangId;

    public function __construct($barangId)
    {
        $this->barangId = $barangId;
    }

    public function handle()
    {
        try {
            $barang = BarangModel::find($this->barangId);
            if (!$barang) {
                Log::warning("Barang not found for stock creation: barang_id={$this->barangId}");
                return;
            }

            $defaultUser = UserModel::find(1); // Adjust to your default user_id
            if (!$defaultUser) {
                Log::warning("Default user not found for stock creation: user_id=1");
                return;
            }

            $latestSupplier = SupplierModel::orderBy('supplier_id', 'desc')->first();
            if (!$latestSupplier) {
                Log::warning("No suppliers found for stock creation: barang_id={$this->barangId}");
                return;
            }

            StockModel::create([
                'barang_id' => $barang->barang_id,
                'supplier_id' => $latestSupplier->supplier_id,
                'user_id' => $defaultUser->user_id,
                'stok_tanggal' => now()->format('Y-m-d'),
                'stok_jumlah' => 0
            ]);

            Log::info("Initial stock created for barang_id={$this->barangId} with supplier_id={$latestSupplier->supplier_id}");
        } catch (\Exception $e) {
            Log::error("Failed to create initial stock for barang_id={$this->barangId}: {$e->getMessage()}");
        }
    }
}
