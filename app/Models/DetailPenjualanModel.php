<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class DetailPenjualanModel extends Model
{
    // Menggunakan trait untuk factory
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model
    protected $table = 't_penjualan_detail';

    // Menentukan primary key tabel
    protected $primaryKey = 'penjualan_detail_id';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = ['penjualan_id', 'barang_id', 'harga', 'jumlah','image',];

    /**
     * Mendefinisikan relasi ke model PenjualanModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function penjualan()
    {
        return $this->belongsTo(PenjualanModel::class, 'penjualan_id');
    }

    /**
     * Mendefinisikan relasi ke model BarangModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => $image ? url('/penjualan/' . $image) : null,
        );
    }
}
