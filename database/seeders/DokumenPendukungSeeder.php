<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DokumenPendukungSeeder extends Seeder
{
    public function run(): void
    {
        $dokumenPendukung = [];

        // Get all dokumen_kriteria
        $kriterias = DB::table('dokumen_kriteria')->get();

        Log::info('DokumenPendukungSeeder: Found ' . count($kriterias) . ' dokumen_kriteria to process.');

        foreach ($kriterias as $kriteria) {
            // Buat 2 dokumen pendukung untuk setiap kriteria
            for ($i = 1; $i <= 2; $i++) {
                $dokumenPendukung[] = [
                    'no_kriteria' => $kriteria->no_kriteria,
                    'id_user' => $kriteria->id_user, // Tambahkan id_user
                    'nama_file' => 'contoh_' . $kriteria->no_kriteria . '_' . $i . '.pdf',
                    'path_file' => 'storage/dokumen/contoh_' . $kriteria->no_kriteria . '_' . $i . '.pdf',
                    'keterangan' => 'Dokumen pendukung ' . $i . ' untuk kriteria ' . $kriteria->no_kriteria,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('dokumen_pendukung')->insert($dokumenPendukung);
    }
}
