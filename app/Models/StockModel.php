<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';
    protected $fillable = ['barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah'];

    protected $dates = ['deleted_at'];
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
