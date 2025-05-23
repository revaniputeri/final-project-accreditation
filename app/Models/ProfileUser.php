<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileUser extends Model
{
    use HasFactory;

    protected $table = 'profile_user';

    protected $primaryKey = 'id_profile';

    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'tempat_tanggal_lahir',
        'nidn',
        'nip',
        'gelar_depan',
        'gelar_belakang',
        'pendidikan_terakhir',
        'pangkat',
        'jabatan_fungsional',
        'no_telp',
        'alamat',
        'created_at',
        'updated_at',
    ];

    // Relationships with all portfolio tables
    public function sertifikasi()
    {
        return $this->hasMany(PSertifikasiModel::class, 'id_user');
    }

    public function kegiatan()
    {
        return $this->hasMany(PKegiatanModel::class, 'id_user');
    }

    public function prestasi()
    {
        return $this->hasMany(PPrestasiModel::class, 'id_user');
    }

    public function organisasi()
    {
        return $this->hasMany(POrganisasiModel::class, 'id_user');
    }

    public function publikasi()
    {
        return $this->hasMany(PPublikasiModel::class, 'id_user');
    }

    public function penelitian()
    {
        return $this->hasMany(PPenelitianModel::class, 'id_user');
    }

    public function karyaBuku()
    {
        return $this->hasMany(PKaryaBukuModel::class, 'id_user');
    }

    public function hki()
    {
        return $this->hasMany(PHKIModel::class, 'id_user');
    }

    public function pengabdian()
    {
        return $this->hasMany(PPengabdianModel::class, 'id_user');
    }

    public function profesi()
    {
        return $this->hasMany(PProfesiModel::class, 'id_user');
    }
}
