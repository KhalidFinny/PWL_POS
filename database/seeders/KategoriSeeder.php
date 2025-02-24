<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori_id' => 1,
                'kategori_kode' => 'CAR',
                'kategori_nama' => 'Mobil Bekas',
            ],
            [
                'kategori_id' => 2,
                'kategori_kode' => 'BK',
                'kategori_nama' => 'Body Kits',
            ],
            [
                'kategori_id' => 3,
                'kategori_kode' => 'ENG',
                'kategori_nama' => 'Aftermarket Engine Parts',
            ],
            [
                'kategori_id' => 4,
                'kategori_kode' => 'RIMS',
                'kategori_nama' => 'Car Rims',
            ],
            [
                'kategori_id' => 5,
                'kategori_kode' => 'INT',
                'kategori_nama' => 'Interior Accessories',
            ],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
