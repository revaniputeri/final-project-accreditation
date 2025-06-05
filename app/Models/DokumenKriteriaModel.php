<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenKriteriaModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dokumen_kriteria';
    protected $primaryKey = 'id_dokumen_kriteria';
    protected $fillable = [
        'id_user',
        'judul',
        'content_html',
        'no_kriteria',
        'versi',
        'kategori',
        'status',
        'id_validator',
        'komentar'
    ];

    protected $casts = [
        'status' => 'string',
        'kategori' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'id_user');
    }

    public function validator()
    {
        return $this->belongsTo(UserModel::class, 'id_validator');
    }

    public function kriteria()
    {
        return $this->belongsTo(KriteriaModel::class, 'no_kriteria', 'no_kriteria');
    }

    public function users()
    {
        // Mengakses semua user yang terkait dengan no_kriteria ini
        return $this->hasManyThrough(
            User::class,
            KriteriaModel::class,
            'no_kriteria', // Foreign key pada tabel kriteria
            'id_user',     // Foreign key pada tabel users
            'no_kriteria', // Local key pada dokumen_kriteria
            'id_user'      // Local key pada kriteria
        );
    }

    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukungModel::class, ['no_kriteria', 'id_user'], ['no_kriteria', 'id_user']);
    }
}
