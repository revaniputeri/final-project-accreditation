<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PHKISeeder extends Seeder
{
    public function run()
    {
        $hkis = [];
        $skema = ['Paten', 'Hak Cipta', 'Merek'];

        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $hkis[] = [
                'id_user' => $user->id_user,
                'judul' => 'Inovasi ' . $user->id_user,
                'tahun' => rand(2018, 2023),
                'skema' => $skema[array_rand($skema)],

                // Key - Nomor tidak boleh sama
                'nomor' => 'HKI-' . rand(1000, 1999),

                'melibatkan_mahasiswa_s2' => rand(0, 1),

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $hkis[] = [
                'id_user' => $user->id_user,
                'judul' => 'Karya ' . $user->id_user,
                'tahun' => rand(2015, 2020),
                'skema' => $skema[array_rand($skema)],

                // Key - Nomor tidak boleh sama
                'nomor' => 'HKI-' . rand(2000, 2999),

                'melibatkan_mahasiswa_s2' => rand(0, 1),

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('p_hki')->insert($hkis);
    }
}
