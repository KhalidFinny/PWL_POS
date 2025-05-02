<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return UserModel::with('level')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer|exists:m_level,level_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
            'level_id' => $request->level_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user,
        ], 201);
    }

    public function show(UserModel $user)
    {
        return $user->load('level');
    }

    public function update(Request $request, UserModel $user)
    {
        $user->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user,
        ], 200);
    }

    public function destroy(UserModel $user)
    {
        try {
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus',
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus user karena terkait dengan data lain.',
            ], 409);
        }
    }
}
