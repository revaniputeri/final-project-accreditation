<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            ['nama_level' => 'Administrator', 'kode_level' => 'ADM'],
            ['nama_level' => 'Anggota (Pengisi Kriteria)', 'kode_level' => 'ANG'],
            ['nama_level' => 'Dosen', 'kode_level' => 'DOS'],
            ['nama_level' => 'Tim Validasi', 'kode_level' => 'VAL'],
            ['nama_level' => 'Direktur', 'kode_level' => 'DIR'],
        ];

        DB::table('level')->insert($levels);
    }
}
