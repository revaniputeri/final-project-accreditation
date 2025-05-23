<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSertifikasiModel extends Model
{
    use HasFactory;

    protected $table = 'p_sertifikasi';
    protected $primaryKey = 'id_sertifikasi';
    protected $fillable = [
        'id_user',
        'tahun_diperoleh',
        'penerbit',
        'nama_sertifikasi',
        'nomor_sertifikat',
        'masa_berlaku',
        'status',
        'sumber_data',
        'bukti'
    ];

    protected $casts = [
        'status' => 'string',
        'sumber_data' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'id_user', 'id_user');
    }

    // Scope for filtering by data source
    public function scopeP3m($query)
    {
        return $query->where('sumber_data', 'p3m');
    }

    public function scopeDosen($query)
    {
        return $query->where('sumber_data', 'dosen');
    }
}
