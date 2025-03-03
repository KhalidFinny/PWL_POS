<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserKontroller extends Controller
{
    public function index()
    {
        $user = UserModel::where('level_id',2)->count();
        return view('user', ['data' => $user]);

    }
}
