<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenPendukungModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dokumen_pendukung';
    protected $primaryKey = 'id_dokumen_pendukung';
    protected $fillable = [
        'no_kriteria',
        'kategori',
        'id_user',
        'nama_file',
        'path_file',
        'keterangan'
    ];

    public function kriteria()
    {
        return $this->belongsTo(KriteriaModel::class, ['no_kriteria', 'id_user'], ['no_kriteria', 'id_user']);
    }

    public function dokumenKriteria()
    {
        return $this->belongsTo(DokumenKriteriaModel::class, ['no_kriteria', 'id_user'], ['no_kriteria', 'id_user']);
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'id_user');
    }
}
