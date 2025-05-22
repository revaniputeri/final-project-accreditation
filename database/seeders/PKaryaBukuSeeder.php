<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PKaryaBukuSeeder extends Seeder
{
    public function run()
    {
        $karyaBukus = [];

        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $karyaBukus[] = [
                'id_user' => $user->id_user,

                // Key - Judul buku tidak boleh sama
                'judul_buku' => 'Buku Ajar ' . $user->id_user,

                'tahun' => rand(2018, 2023),
                'jumlah_halaman' => rand(100, 300),
                'penerbit' => 'Penerbit ' . $user->id_user,
                'isbn' => 'ISBN-' . rand(800000, 999999),

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $karyaBukus[] = [
                'id_user' => $user->id_user,

                // Key - Judul buku tidak boleh sama
                'judul_buku' => 'Modul Pembelajaran ' . $user->id_user,

                'tahun' => rand(2015, 2020),
                'jumlah_halaman' => rand(50, 150),
                'penerbit' => 'Penerbit ' . ($user->id_user + 1),
                'isbn' => 'ISBN-' . rand(700000, 899999),

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('p_karya_buku')->insert($karyaBukus);
    }
}
