<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PSertifikasiSeeder extends Seeder
{
    public function run()
    {
        $sertifikasis = [];
        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            // 2 sertifikasi untuk setiap dosen
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $sertifikasis[] = [
                'id_user' => $user->id_user,
                'tahun_diperoleh' => rand(2018, 2023),
                'penerbit' => 'Lembaga Sertifikasi Profesi ' . rand(1, 5),
                'nama_sertifikasi' => 'Sertifikasi Kompetensi Bidang ' . ['IT', 'Pendidikan', 'Manajemen', 'Teknik'][rand(0, 3)],

                // Key - Nomor Sertifikat tidak boleh sama
                'nomor_sertifikat' => 'SKB-' . rand(1000, 9999),

                'masa_berlaku' => rand(1, 5) . ' Tahun',

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $sertifikasis[] = [
                'id_user' => $user->id_user,
                'tahun_diperoleh' => rand(2015, 2020),
                'penerbit' => 'Asosiasi Profesi ' . ['Indonesia', 'Nasional', 'Internasional'][rand(0, 2)],
                'nama_sertifikasi' => 'Sertifikasi Keahlian ' . ['Khusus', 'Profesional', 'Teknis'][rand(0, 2)],

                // Key - Nomor Sertifikat tidak boleh sama
                'nomor_sertifikat' => 'SKK-' . rand(1000, 9999),

                'masa_berlaku' => rand(1, 3) . ' Tahun',

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('p_sertifikasi')->insert($sertifikasis);
    }
}
