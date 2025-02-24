<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'barang_id' => 1,
                'kategori_id' => 1,
                'brang_kode' => 'LFA',
                'barang_nama' => 'Lexus LFA',
                'harga_beli' => 1500000000,
                'harga_jual' => 2000000000,
            ],
            [
                'barang_id' => 2,
                'kategori_id' => 2,
                'brang_kode' => 'HKS',
                'barang_nama' => 'HKS LFA Kit',
                'harga_beli' => 150000000,
                'harga_jual' => 300000000,
            ],
            [
                'barang_id' => 3,
                'kategori_id' => 3,
                'brang_kode' => 'Top Secret',
                'barang_nama' => 'Top Secret Engine kit',
                'harga_beli' => 30000000,
                'harga_jual' => 45000000,
            ],
            [
                'barang_id' => 4,
                'kategori_id' => 4,
                'brang_kode' => 'BBS',
                'barang_nama' => 'BBS SR',
                'harga_beli' => 20000000,
                'harga_jual' => 25000000,
            ],
            [
                'barang_id' => 5,
                'kategori_id' => 5,
                'brang_kode' => 'Recaro',
                'barang_nama' => 'Recaro Shell Seats',
                'harga_beli' => 5000000,
                'harga_jual' => 6000000,
            ],
            [
                'barang_id' => 6,
                'kategori_id' => 1,
                'brang_kode' => 'HND',
                'barang_nama' => 'Honda S2000',
                'harga_beli' => 670000000,
                'harga_jual' => 700000000,
            ],
            [
                'barang_id' => 7,
                'kategori_id' => 2,
                'brang_kode' => 'MS',
                'barang_nama' => 'Mugen Body Set',
                'harga_beli' => 150000000,
                'harga_jual' => 200000000,
            ],
            [
                'barang_id' => 8,
                'kategori_id' => 3,
                'brang_kode' => 'MGF20C',
                'barang_nama' => 'Honda F20C Mugen Treatement',
                'harga_beli' => 150000000,
                'harga_jual' => 250000000,
            ],
            [
                'barang_id' => 9,
                'kategori_id' => 4,
                'brang_kode' => 'MGGP',
                'barang_nama' => 'Mugen GP',
                'harga_beli' => 15000000,
                'harga_jual' => 20000000,
            ],
            [
                'barang_id' => 10,
                'kategori_id' => 5,
                'brang_kode' => 'MGINT',
                'barang_nama' => 'Mugen racing Sets',
                'harga_beli' => 10000000,
                'harga_jual' => 20000000,
            ],
            [
                'barang_id' => 11,
                'kategori_id' => 1,
                'brang_kode' => '240Z',
                'barang_nama' => 'Nissan 240Z',
                'harga_beli' => 750000000,
                'harga_jual' => 800000000,
            ],
            [
                'barang_id' => 12,
                'kategori_id' => 2,
                'brang_kode' => 'BUNNY',
                'barang_nama' => 'Rocket Bunny Body Kit',
                'harga_beli' => 120000000,
                'harga_jual' => 150000000,
            ],
            [
                'barang_id' => 13,
                'kategori_id' => 3,
                'brang_kode' => 'NSMRB26',
                'barang_nama' => 'Nismo Tuned RB26DETT',
                'harga_beli' => 250000000,
                'harga_jual' => 450000000,
            ],
            [
                'barang_id' => 14,
                'kategori_id' => 4,
                'brang_kode' => 'WAT',
                'barang_nama' => 'RS Watanabe R-Type',
                'harga_beli' => 6000000,
                'harga_jual' => 7000000,
            ],
            [
                'barang_id' => 15,
                'kategori_id' => 5,
                'brang_kode' => 'NINTS',
                'barang_nama' => 'Nismo Interior Set',
                'harga_beli' => 54000000,
                'harga_jual' => 60000000,
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
