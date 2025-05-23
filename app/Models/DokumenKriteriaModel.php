<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenKriteriaModel extends Model
{
    use HasFactory;

    protected $table = 'dokumen_kriteria';
    protected $primaryKey = 'id_dokumen_kriteria';
    protected $fillable = [
        'id_user',
        'judul',
        'content_html',
        'status',
        'komentar',
        'id_validator'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'id_user');
    }

    public function validator()
    {
        return $this->belongsTo(UserModel::class, 'id_validator');
    }

    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukungModel::class, 'id_dokumen_kriteria');
    }
}
