<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            [
                'penjualan_id' => 1,
                'user_id' => 1,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'NSN1',
                'Penjualan Tanggal' => '2025-02-01',
            ],
            [
                'penjualan_id' => 2,
                'user_id' => 3,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'NSN2',
                'Penjualan Tanggal' => '2025-02-02',
            ],
            [
                'penjualan_id' => 3,
                'user_id' => 3,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'NSN3',
                'Penjualan Tanggal' => '2025-02-03',
            ],
            [
                'penjualan_id' => 4,
                'user_id' => 1,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'NSN4',
                'Penjualan Tanggal' => '2025-02-04',
            ],
            [
                'penjualan_id' => 5,
                'user_id' => 3,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'NSN5',
                'Penjualan Tanggal' => '2025-02-05',
            ],
            [
                'penjualan_id' => 6,
                'user_id' => 3,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'GR1',
                'Penjualan Tanggal' => '2025-02-06',
            ],
            [
                'penjualan_id' => 7,
                'user_id' => 1,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'GR2',
                'Penjualan Tanggal' => '2025-02-07',
            ],
            [
                'penjualan_id' => 8,
                'user_id' => 3,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'GR4',
                'Penjualan Tanggal' => '2025-02-08',
            ],
            [
                'penjualan_id' => 9,
                'user_id' => 3,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'GR5',
                'Penjualan Tanggal' => '2025-02-09',
            ],
            [
                'penjualan_id' => 10,
                'user_id' => 1,
                'pembeli' => 'Khalid',
                'penjualan_kode' => 'GR6',
                'Penjualan Tanggal' => '2025-02-10',
            ],

        ];
        DB::table('t_penjualan')->insert($data);
    }
}
