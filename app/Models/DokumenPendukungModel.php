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
        'id_dokumen_kriteria',
        'nama_file',
        'path_file',
        'keterangan'
    ];

    public function dokumenKriteria()
    {
        return $this->belongsTo(DokumenKriteriaModel::class, 'id_dokumen_kriteria');
    }
}
