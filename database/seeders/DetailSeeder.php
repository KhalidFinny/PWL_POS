<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Barang 1 (Lexus LFA) - 3 penjualan
            [
                'penjualan_detail_id' => 1,
                'penjualan_id' => 1,
                'barang_id' => 1,
                'harga' => 2000000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 2,
                'penjualan_id' => 2,
                'barang_id' => 1,
                'harga' => 2000000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 3,
                'penjualan_id' => 3,
                'barang_id' => 1,
                'harga' => 2000000000,
                'jumlah' => 1,
            ],

            // Barang 2 (HKS LFA Kit) - 3 penjualan
            [
                'penjualan_detail_id' => 4,
                'penjualan_id' => 4,
                'barang_id' => 2,
                'harga' => 300000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 5,
                'penjualan_id' => 5,
                'barang_id' => 2,
                'harga' => 300000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 6,
                'penjualan_id' => 6,
                'barang_id' => 2,
                'harga' => 300000000,
                'jumlah' => 1,
            ],

            // Barang 3 (Top Secret Engine kit) - 3 penjualan
            [
                'penjualan_detail_id' => 7,
                'penjualan_id' => 7,
                'barang_id' => 3,
                'harga' => 45000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 8,
                'penjualan_id' => 8,
                'barang_id' => 3,
                'harga' => 45000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 9,
                'penjualan_id' => 9,
                'barang_id' => 3,
                'harga' => 45000000,
                'jumlah' => 1,
            ],

            // Barang 4 (BBS SR) - 3 penjualan
            [
                'penjualan_detail_id' => 10,
                'penjualan_id' => 10,
                'barang_id' => 4,
                'harga' => 25000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 11,
                'penjualan_id' => 1,
                'barang_id' => 4,
                'harga' => 25000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 12,
                'penjualan_id' => 2,
                'barang_id' => 4,
                'harga' => 25000000,
                'jumlah' => 1,
            ],

            // Barang 5 (Recaro Shell Seats) - 3 penjualan
            [
                'penjualan_detail_id' => 13,
                'penjualan_id' => 3,
                'barang_id' => 5,
                'harga' => 6000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 14,
                'penjualan_id' => 4,
                'barang_id' => 5,
                'harga' => 6000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 15,
                'penjualan_id' => 5,
                'barang_id' => 5,
                'harga' => 6000000,
                'jumlah' => 1,
            ],

            // Barang 6 (Honda S2000) - 3 penjualan
            [
                'penjualan_detail_id' => 16,
                'penjualan_id' => 6,
                'barang_id' => 6,
                'harga' => 700000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 17,
                'penjualan_id' => 7,
                'barang_id' => 6,
                'harga' => 700000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 18,
                'penjualan_id' => 8,
                'barang_id' => 6,
                'harga' => 700000000,
                'jumlah' => 1,
            ],

            // Barang 7 (Mugen Body Set) - 3 penjualan
            [
                'penjualan_detail_id' => 19,
                'penjualan_id' => 9,
                'barang_id' => 7,
                'harga' => 200000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 20,
                'penjualan_id' => 10,
                'barang_id' => 7,
                'harga' => 200000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 21,
                'penjualan_id' => 1,
                'barang_id' => 7,
                'harga' => 200000000,
                'jumlah' => 1,
            ],

            // Barang 8 (Honda F20C Mugen Treatment) - 3 penjualan
            [
                'penjualan_detail_id' => 22,
                'penjualan_id' => 2,
                'barang_id' => 8,
                'harga' => 250000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 23,
                'penjualan_id' => 3,
                'barang_id' => 8,
                'harga' => 250000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 24,
                'penjualan_id' => 4,
                'barang_id' => 8,
                'harga' => 250000000,
                'jumlah' => 1,
            ],

            // Barang 9 (Mugen GP) - 3 penjualan
            [
                'penjualan_detail_id' => 25,
                'penjualan_id' => 5,
                'barang_id' => 9,
                'harga' => 20000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 26,
                'penjualan_id' => 6,
                'barang_id' => 9,
                'harga' => 20000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 27,
                'penjualan_id' => 7,
                'barang_id' => 9,
                'harga' => 20000000,
                'jumlah' => 1,
            ],

            // Barang 10 (Mugen Racing Sets) - 3 penjualan
            [
                'penjualan_detail_id' => 28,
                'penjualan_id' => 8,
                'barang_id' => 10,
                'harga' => 20000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 29,
                'penjualan_id' => 9,
                'barang_id' => 10,
                'harga' => 20000000,
                'jumlah' => 1,
            ],
            [
                'penjualan_detail_id' => 30,
                'penjualan_id' => 10,
                'barang_id' => 10,
                'harga' => 20000000,
                'jumlah' => 1,
            ],
        ];

        DB::table('t_penjualan_detail')->insert($data);
    }
}
