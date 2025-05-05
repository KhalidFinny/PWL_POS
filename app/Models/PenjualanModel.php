<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class PenjualanModel extends Model
{
    // Menggunakan trait untuk factory dan soft delete
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model
    protected $table = 't_penjualan';

    // Menentukan primary key tabel
    protected $primaryKey = 'penjualan_id';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = ['user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal','image',];

    // Kolom yang akan dikonversi menjadi instance Carbon untuk manipulasi tanggal
    protected $dates = ['deleted_at'];

    // Menentukan nilai default untuk kolom user_id
    protected $attributes = [
        'user_id' => 1,
    ];

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
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => $image ? url('/penjualan/' . $image) : null,
        );
    }

}
