<?php

namespace App\Jobs;

use App\Models\BarangModel;
use App\Models\StockModel;
use App\Models\UserModel;
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
            $barang = BarangModel::with('supplier')->find($this->barangId);

            if (!$barang) {
                Log::warning("Barang not found for stock creation: barang_id={$this->barangId}");
                return;
            }

            $defaultUser = UserModel::first();

            if (!$defaultUser) {
                Log::warning("No users found in database for stock creation");
                return;
            }

            if (!$barang->supplier_id) {
                Log::warning("Barang has no supplier assigned: barang_id={$this->barangId}");
                return;
            }

            StockModel::create([
                'barang_id' => $barang->barang_id,
                'supplier_id' => $barang->supplier_id,
                'user_id' => $defaultUser->user_id,
                'stok_tanggal' => now()->format('Y-m-d H:i:s'),
                'stok_jumlah' => 1
            ]);

            Log::info("Initial stock created successfully", [
                'barang_id' => $this->barangId,
                'supplier_id' => $barang->supplier_id
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to create initial stock", [
                'barang_id' => $this->barangId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
