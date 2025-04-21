<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockModel extends Model
{
    // Menggunakan trait untuk factory dan soft delete
    use HasFactory, SoftDeletes;

    // Menentukan nama tabel yang digunakan oleh model
    protected $table = 't_stok';

    // Menentukan primary key tabel
    protected $primaryKey = 'stok_id';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = ['barang_id', 'supplier_id', 'user_id', 'stok_tanggal', 'stok_jumlah'];

    // Kolom yang akan dikonversi menjadi instance Carbon untuk manipulasi tanggal
    protected $dates = ['deleted_at'];

    /**
     * Mendefinisikan relasi ke model BarangModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    /**
     * Mendefinisikan relasi ke model SupplierModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id');
    }

    /**
     * Mendefinisikan relasi ke model UserModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
