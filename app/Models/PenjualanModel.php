<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenjualanModel extends Model
{
    // Menggunakan trait untuk factory dan soft delete
    use HasFactory, SoftDeletes;

    // Menentukan nama tabel yang digunakan oleh model
    protected $table = 't_penjualan';

    // Menentukan primary key tabel
    protected $primaryKey = 'penjualan_id';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = ['user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal'];

    // Kolom yang akan dikonversi menjadi instance Carbon untuk manipulasi tanggal
    protected $dates = ['deleted_at'];

    /**
     * Mendefinisikan relasi ke model UserModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /**
     * Mendefinisikan relasi ke model DetailPenjualanModel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(DetailPenjualanModel::class, 'penjualan_id');
    }
}
