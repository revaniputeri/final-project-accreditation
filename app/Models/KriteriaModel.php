<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaModel extends Model
{
    use HasFactory;

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
        return $this->hasMany(DokumenKriteriaModel::class, 'no_kriteria', 'no_kriteria')
                   ->whereColumn('dokumen_kriteria.id_user', 'kriteria.id_user');
    }

    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukungModel::class, 'no_kriteria', 'no_kriteria')
                   ->whereColumn('dokumen_pendukung.id_user', 'kriteria.id_user');
    }

    public function latestDokumen()
    {
        return $this->hasOne(DokumenKriteriaModel::class, 'no_kriteria', 'no_kriteria')
                   ->whereColumn('dokumen_kriteria.id_user', 'kriteria.id_user')
                   ->latest('versi');
    }
}
