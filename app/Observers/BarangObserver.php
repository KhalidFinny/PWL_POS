<?php

namespace App\Observers;

use App\Models\BarangModel;
use App\Jobs\CreateInitialStockJob;

class BarangObserver
{
    public function created(BarangModel $barang)
    {
        // Dispatch a queued job to create the initial stock record
        CreateInitialStockJob::dispatch($barang->barang_id);
    }
}
