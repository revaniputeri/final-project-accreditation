<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokumenKriteriaSeeder extends Seeder
{
    public function run()
    {
        $dokumenKriteria = [];

        $angUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'ANG')
            ->get();

        foreach ($angUsers as $user) {
            $dokumenKriteria[] = [
                'id_user' => $user->id_user,
                'judul' => 'Kriteria untuk user ' . $user->id_user,
                'content_html' => '',
                'status' => 'kosong',
                'id_validator' => null,
                'komentar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('dokumen_kriteria')->insert($dokumenKriteria);
    }
}
