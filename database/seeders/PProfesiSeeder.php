<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PProfesiSeeder extends Seeder
{
    public function run()
    {
        $profesis = [];

        $dosenUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'DOS')
            ->get();

        foreach ($dosenUsers as $user) {
            $sumber_data = ['p3m', 'dosen'][rand(0, 1)];
            $profesis[] = [
                'id_user' => $user->id_user,
                'perguruan_tinggi' => 'Universitas ' . $user->id_user,
                'kurun_waktu' => (rand(2000, 2005)) . '-' . (rand(2006, 2010)),
                'gelar' => 'Doktor',
                'status' => 'tervalidasi',

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'updated_at' => now(),
            ];

            $profesis[] = [
                'id_user' => $user->id_user,
                'perguruan_tinggi' => 'Institut ' . $user->id_user,
                'kurun_waktu' => (rand(2005, 2010)) . '-' . (rand(2011, 2015)),
                'gelar' => 'Profesor',
                'status' => 'tervalidasi',

                // Status sertifikasi tergantung pada sumber data
                'status' => $sumber_data === 'dosen' ? 'tervalidasi' : ['perlu validasi', 'tidak valid'][rand(0, 1)],
                'sumber_data' => $sumber_data,
                'bukti' => $sumber_data === 'dosen' ? 'contoh.pdf' : null,

                'updated_at' => now(),
            ];
        }

        DB::table('p_profesi')->insert($profesis);
    }
}
