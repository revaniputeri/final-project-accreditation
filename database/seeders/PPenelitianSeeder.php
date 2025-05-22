<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PPenelitianSeeder extends Seeder
{
    public function run()
    {
        $penelitians = [];
        $skema = ['Hibah Dikti', 'Hibah Internal', 'Kerjasama Industri', 'Mandiri'];

        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $penelitians[] = [
                'id_user' => $user->id_user,

                // Key - Judul tidak boleh sama
                'judul_penelitian' => 'Penelitian Dasar ' . $user->id_user,

                'skema' => $skema[array_rand($skema)],
                'tahun' => rand(2018, 2023),
                'dana' => rand(5000000, 20000000),
                'peran' => (rand(0, 1) ? 'ketua' : 'anggota'),
                'melibatkan_mahasiswa_s2' => rand(0, 1),

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $penelitians[] = [
                'id_user' => $user->id_user,

                // Key - Judul tidak boleh sama
                'judul_penelitian' => 'Penelitian Terapan ' . $user->id_user,

                'skema' => $skema[array_rand($skema)],
                'tahun' => rand(2015, 2020),
                'dana' => rand(3000000, 15000000),
                'peran' => (rand(0, 1) ? 'ketua' : 'anggota'),
                'melibatkan_mahasiswa_s2' => rand(0, 1),

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('p_penelitian')->insert($penelitians);
    }
}
