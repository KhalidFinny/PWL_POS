<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = ['level_id', 'username', 'nama', 'password', 'profile_picture']; 
    protected $hidden = ['password'];
    protected $casts = ['password' => 'hashed'];

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function hasRole($role): bool
    {
        return $this->level->level_kode == $role;
    }

    public function getRole()
    {
        return $this->level->level_kode;
    }

    public function getProfilePictureUrl()
    {
        $url = $this->profile_picture ? Storage::url($this->profile_picture) : asset('defualt.png');
        Log::info('Generated Profile Picture URL for user ' . $this->user_id . ': ' . $url);
        return $url;
    }
}
