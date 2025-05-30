<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPendukungModel extends Model
{
    use HasFactory;

    protected $table = 'dokumen_pendukung';
    protected $primaryKey = 'id_dokumen_pendukung';
    protected $fillable = [
        'no_kriteria',
        'nama_file',
        'path_file',
        'keterangan'
    ];

    public function dokumenKriteria()
    {
        return $this->belongsTo(DokumenKriteriaModel::class, 'no_kriteria', 'no_kriteria');
    }
}
