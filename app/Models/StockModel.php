<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockModel extends Model
{

    use HasFactory;
    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';
    public $timestamps = false;

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}

