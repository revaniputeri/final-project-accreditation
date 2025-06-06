<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PHKIModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'p_hki';
    protected $primaryKey = 'id_hki';
    protected $fillable = [
        'id_user',
        'judul',
        'tahun',
        'skema',
        'nomor',
        'melibatkan_mahasiswa_s2',
        'status',
        'sumber_data',
        'bukti'
    ];

    protected $casts = [
        'status' => 'string',
        'sumber_data' => 'string'
    ];

    public function dosen()
    {
        return $this->belongsTo(UserModel::class, 'id_user');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
