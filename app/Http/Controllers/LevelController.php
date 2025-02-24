<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
    //     DB::insert('insert into m_level
    // (level_kode, level_nama, created_at) values (?, ?, ?)',
    // ['CUS', 'Customer', now()]);

    // $row = DB::update('update m_level set level_nama =
    // ? where level_kode = ?', ['Customer', 'CUS']);

    // return 'update data berhasil, jumlah data yang diupdate: '.$row. ' baris';

    // return 'Insert data baru berhasil';

    $data = DB::select("SELECT * FROM m_level");
    return view('level', ['data' => $data]);
    }
}
