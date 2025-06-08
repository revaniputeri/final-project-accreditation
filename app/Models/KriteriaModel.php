<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KriteriaModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kriteria';
    protected $primaryKey = ['no_kriteria', 'id_user'];
    public $incrementing = false;
    protected $fillable = [
        'no_kriteria',
        'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'id_user');
    }

    public function dokumenKriteria()
    {
        return $this->hasMany(DokumenKriteriaModel::class, 'no_kriteria', 'no_kriteria');
    }

    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukungModel::class, 'no_kriteria', 'no_kriteria');
    }

    public function latestDokumen()
    {
        return $this->hasOne(DokumenKriteriaModel::class, 'no_kriteria', 'no_kriteria')
            ->latest('versi');
    }
    public function dokumen()
    {
        return $this->hasMany(DokumenKriteriaModel::class, 'kriteria_id');
    }

    public function profile_user()
    {
        return $this->belongsTo(ProfileUser::class, 'id_user');
    }
}
