<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DokumenKriteriaSeeder extends Seeder
{
    public function run()
    {
        $dokumenKriteria = [];

        // Ambil semua data kriteria yang sudah dibuat
        $kriterias = DB::table('kriteria')->get();

        Log::info('DokumenKriteriaSeeder: Found ' . count($kriterias) . ' kriteria to process.');

        foreach ($kriterias as $kriteria) {
            $dokumenKriteria[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'versi' => 1, // Versi awal
                'id_user' => $kriteria->id_user,
                'judul' => 'Dokumen Kriteria ' . $kriteria->no_kriteria . ' - User ' . $kriteria->id_user,
                'content_html' => '<p>Konten default untuk kriteria ' . $kriteria->no_kriteria . '</p>',
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
