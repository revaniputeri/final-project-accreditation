<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPenelitianModel extends Model
{
    use HasFactory;

    protected $table = 'p_penelitian';
    protected $primaryKey = 'id_penelitian';
    protected $fillable = [
        'id_user',
        'judul_penelitian',
        'skema',
        'tahun',
        'dana',
        'peran',
        'melibatkan_mahasiswa_s2',
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
        return $this->belongsTo(UserModel::class, 'id_user');
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
