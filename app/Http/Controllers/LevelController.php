<?php

namespace App\Http\Controllers;
use App\Models\LevelModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Level User',
            'list' => ['Home', 'Data User']
        ];

        $page = (object) [
            'title' => 'Level User yang terdaftar'
        ];
        $activeMenu = 'level';
        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }


    public function list(Request $request)
    {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama');
        return DataTables::of($level)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                $btn = '<a href="' . url('/user/' . $level->level_id) . '" class="btn btn-info btn-
                sm">Detail</a> ';
                $btn .= '<a href="' . url('/user/' . $level->level_id . '/edit') . '" class="btn btn-
                warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' .
                    url('/user/' . $level->level_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return
                confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
