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

        // Ambil semua no_kriteria unik (tanpa duplikat)
        $uniqueKriterias = DB::table('kriteria')
            ->select('no_kriteria')
            ->distinct()
            ->get();

        Log::info('DokumenKriteriaSeeder: Found ' . count($uniqueKriterias) . ' unique kriteria to process.');

        foreach ($uniqueKriterias as $kriteria) {
            $dokumenKriteria[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'versi' => 1, // Versi awal
                'judul' => 'Dokumen Kriteria ' . $kriteria->no_kriteria,
                'kategori' => 'penetapan',
                'content_html' => '<p>Konten default untuk kriteria ' . $kriteria->no_kriteria . '</p>',
                'status' => 'kosong',
                'id_validator' => null,
                'komentar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $dokumenKriteria[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'versi' => 1, // Versi awal
                'judul' => 'Dokumen Kriteria ' . $kriteria->no_kriteria,
                'kategori' => 'pelaksanaan',
                'content_html' => '<p>Konten default untuk kriteria ' . $kriteria->no_kriteria . '</p>',
                'status' => 'kosong',
                'id_validator' => null,
                'komentar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $dokumenKriteria[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'versi' => 1, // Versi awal
                'judul' => 'Dokumen Kriteria ' . $kriteria->no_kriteria,
                'kategori' => 'evaluasi',
                'content_html' => '<p>Konten default untuk kriteria ' . $kriteria->no_kriteria . '</p>',
                'status' => 'kosong',
                'id_validator' => null,
                'komentar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $dokumenKriteria[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'versi' => 1, // Versi awal
                'judul' => 'Dokumen Kriteria ' . $kriteria->no_kriteria,
                'kategori' => 'pengendalian',
                'content_html' => '<p>Konten default untuk kriteria ' . $kriteria->no_kriteria . '</p>',
                'status' => 'kosong',
                'id_validator' => null,
                'komentar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $dokumenKriteria[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'versi' => 1, // Versi awal
                'judul' => 'Dokumen Kriteria ' . $kriteria->no_kriteria,
                'kategori' => 'peningkatan',
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
