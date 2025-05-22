<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'username',
        'password',
        'id_level'
    ];

    protected $hidden = ['password'];

    protected $cast = ['password' => 'hashed'];

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'id_level');
    }

    public function profile()
    {
        return $this->hasOne(ProfileUser::class, 'id_user', 'id_user');
    }

    public function hasRoleName(): string
    {
        return $this->level->kode_level;
    }

    public function hasRole($role): bool
    {
        return $this->level->kode_level === $role;
    }

    public function getRole()
    {
        return $this->level->kode_level;
    }

    public function getRoleNames()
    {
        return [$this->level->kode_level]; // Kembalikan sebagai array
    }

    public function dokumenKriteria()
    {
        return $this->hasMany(DokumenKriteriaModel::class, 'id_user');
    }

    public function validatedDokumen()
    {
        return $this->hasMany(DokumenKriteriaModel::class, 'id_validator');
    }
}
