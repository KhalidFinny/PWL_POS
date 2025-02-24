<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Kategoricontroller extends Controller
{
    public function index()
    {
        $data = [
            [
                'kategori_kode' => 'SND',
                'kategori_nama' => 'Car Speaker',
                'created_at' => now(),
            ],
        ];
        // DB::table('m_kategori')->insert($data);
        // return 'insert data baru berhasil';

        // $row = DB::table('m_kategori')->where('kategori_kode', 'SND')
        // ->delete();
        // return 'Delete data berhasil, jumlah data yang diupdate: '.$row. ' baris';

        $data = DB::table('m_kategori')->get();
        return view('kategori', ['data' => $data]);
    }

}
