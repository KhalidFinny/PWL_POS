<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $data = [
            [
                'supplier_id' => 1,
                'supplier_kode' => 'TYLGR',
                'supplier_nama' => 'Toyota Lexus Gazoo Racing',
                'supplier_alamat' => 'Toyota Allee 7, 50858 Köln, Germany',
            ],
            [
                'supplier_id' => 2,
                'supplier_kode' => 'HNMGN',
                'supplier_nama' => 'Honda Mugen',
                'supplier_alamat' => 'Jl. Raya Pasar Minggu No.10, Kalibata, Kec. Pancoran, Kota Jakarta Selatan, Jakarta, Indonesia 12470',
            ],
            [
                'supplier_id' => 3,
                'supplier_kode' => 'NSM',
                'supplier_nama' => 'Nissan Nismo',
                'supplier_alamat' => 'Yokohama, Japan',
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
