<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PKaryaBukuModel extends Model
{
    use HasFactory;

    protected $table = 'p_karya_buku';
    protected $primaryKey = 'id_karya_buku';
    protected $fillable = [
        'id_user',
        'judul_buku',
        'tahun',
        'jumlah_halaman',
        'penerbit',
        'isbn',
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
