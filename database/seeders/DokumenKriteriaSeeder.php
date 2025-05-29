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
            // Removed the id_user range filter to include all users with level 'ANG'
            ->get();

        // Nomor kriteria unik untuk setiap user (1-5)
        $noKriteria = 1;

        // Debug: Log the number of users found
        info('DokumenKriteriaSeeder: Found ' . count($angUsers) . ' users with level ANG.');

        foreach ($angUsers as $user) {
            $dokumenKriteria[] = [
                'no_kriteria' => $noKriteria++, // Nomor kriteria increment (1-5)
                'versi' => 1, // Versi awal
                'id_user' => $user->id_user,
                'judul' => 'Dokumen Kriteria ' . ($noKriteria-1) . ' - User ' . $user->id_user,
                'content_html' => '<p>Konten default untuk kriteria ' . ($noKriteria-1) . '</p>',
                'status' => 'kosong',
                'id_validator' => null,
                'komentar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Reset nomor kriteria jika melebihi 5
            if ($noKriteria > 5) $noKriteria = 1;
        }

        DB::table('dokumen_kriteria')->insert($dokumenKriteria);
    }
}
