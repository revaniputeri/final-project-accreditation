<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class POrganisasiSeeder extends Seeder
{
    public function run()
    {
        $organisasis = [];
        $tingkat = ['Nasional', 'Internasional'];

        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $organisasis[] = [
                'id_user' => $user->id_user,

                // Key - Nama organisasi tidak boleh sama
                'nama_organisasi' => 'Asosiasi Profesi ' . $user->id_user,

                'kurun_waktu' => (rand(2015, 2018)) . '-Sekarang',
                'tingkat' => $tingkat[array_rand($tingkat)],

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            $organisasis[] = [
                'id_user' => $user->id_user,

                // Key - Nama organisasi tidak boleh sama
                'nama_organisasi' => 'Ikatan Dosen ' . $user->id_user,

                'kurun_waktu' => (rand(2018, 2020)) . '-Sekarang',
                'tingkat' => $tingkat[array_rand($tingkat)],

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('p_organisasi')->insert($organisasis);
    }
}
