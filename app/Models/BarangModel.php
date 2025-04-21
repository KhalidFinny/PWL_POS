<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    // Menggunakan trait untuk factory
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model
    protected $table = 'm_barang';

    // Menentukan primary key tabel
    protected $primaryKey = 'barang_id';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = ['kategori_id', 'supplier_id', 'brang_kode', 'barang_nama', 'harga_beli', 'harga_jual'];

    /**
     * Mendefinisikan relasi ke model KategoriModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Mendefinisikan relasi ke model SupplierModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Mendefinisikan relasi ke model StockModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stok()
    {
        return $this->hasMany(StockModel::class, 'barang_id', 'barang_id');
    }
}
