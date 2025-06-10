<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PKegiatanSeeder extends Seeder
{
    public function run()
    {
        $kegiatans = [];
        $jenis_kegiatan = ['Lokakarya', 'Workshop', 'Pagelaran', 'Peragaan', 'Pelatihan', 'Lain_lain'];
        $peran = ['penyaji', 'peserta', 'lainnya'];

        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $kegiatans[] = [
                'id_user' => $user->id_user,
                'jenis_kegiatan' => $jenis_kegiatan[array_rand($jenis_kegiatan)],
                'tempat' => 'Universitas ' . $user->id_user,
                'waktu' => date('Y-m-d', strtotime('-' . rand(1, 12) . ' months')),
                'peran' => $peran[array_rand($peran)],

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $kegiatans[] = [
                'id_user' => $user->id_user,
                'jenis_kegiatan' => $jenis_kegiatan[array_rand($jenis_kegiatan)],
                'tempat' => 'Kampus ' . ($user->id_user + 1),
                'waktu' => date('Y-m-d', strtotime('-' . rand(1, 12) . ' months')),
                'peran' => $peran[array_rand($peran)],

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('p_kegiatan')->insert($kegiatans);
    }
}
