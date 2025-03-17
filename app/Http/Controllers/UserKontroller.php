<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\LevelModel;

class UserKontroller extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];

        $page = (object) [
            'title' => 'Daftar User yang terdaftar di sistem'
        ];

        $activeMenu = 'user';

        $level = LevelModel::all();

        return view('user.index', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    // Ambil data user dalam bentuk json untuk datatables
    // Ambil data user dalam bentuk json untuk datatables
public function list(Request $request)
{
$users = UserModel::select('user_id', 'username', 'nama', 'level_id')
->with('level');
// Filter data user berdasarkan level_id
if ($request->level_id){
$users->where('level_id',$request->level_id);
}
return DataTables::of($users)
->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom:DT_RowIndex)
->addColumn('aksi', function ($user) { // menambahkan kolom aksi


    $btn = '<button onclick="modalAction(\''.url('/user/' . $user->user_id .
'/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
$btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id .
'/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
$btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id .
'/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
return $btn;
})
->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
->make(true);
}



    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah User Baru'
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.create', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3| unique:m_user, username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer'
        ]);
        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }

    public function show(String $id)
    {
        $user = UserModel::with('level')->find($id);
        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail']
        ];
        $page = (object) [
            'title' => 'Detail user'
        ];
        $activeMenu = 'user'; // set menu yang sedang aktif
        return view('user.show', compact('breadcrumb', 'page', 'user', 'activeMenu'));
    }

    public function edit(String $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list' => ['Home', 'User', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit user',
        ];
        $activeMenu = 'user';
        return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([


            'username' => 'required|string |min: 3 unique:m_user, username, ' . $id . ', user_id',
            'nama' => 'required string | max: 100',
            'password' => 'nullable |min:5',
            'level_id' => 'required|integer'
        ]);

        UserModel::find($id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id' => $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    // Menghapus data user
    public function destroy(string $id)
    {
        $check = UserModel::find($id);
        // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
        if (!$check) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }
        try {
            UserModel::destroy($id);
            // Hapus data level
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.create_ajax', compact('level'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:6'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            UserModel::create([
                'username' => $request->username,
                'nama' => $request->nama,
                'password' => bcrypt($request->password),
                'level_id' => $request->level_id
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }
        return redirect('/');
    }
    public function edit_ajax(string $id){
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.edit_ajax', compact('user', 'level'));
    }
    public function update_ajax(Request $request, string $id){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
                'nama' => 'required|string|max:100',
                'password' => 'nullable|min:6|max:20'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
            $check = UserModel::find($id);
            if ($check) {
                if (!$request->filled('password')) {
                    $request->request->remove('password');
                }
                $check->update($request->all());
            return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate'
            ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                    ]);
            }
        }

        return redirect('/');
    }
    public function show_ajax(String $id)
     {
         $user = UserModel::find($id);
         $level = LevelModel::select('level_id', 'level_nama')->get();

         return view('user.show_ajax', compact('user', 'level'));
     }
}
