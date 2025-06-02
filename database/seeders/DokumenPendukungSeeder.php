<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class DokumenPendukungSeeder extends Seeder
{
    public function run(): void
    {
        $dokumenPendukung = [];

        // Ambil semua user dengan role ANG
        $angUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'ANG')
            ->get();

        Log::info('DokumenPendukungSeeder: Found ' . count($angUsers) . ' users with role ANG.');

        // Ambil semua no_kriteria unik dari dokumen_kriteria
        $uniqueKriterias = DB::table('dokumen_kriteria')
            ->select('no_kriteria')
            ->distinct()
            ->get();

        foreach ($uniqueKriterias as $kriteria) {
            // Untuk setiap kriteria, ambil 1 user ANG secara acak
            $randomUser = $angUsers->random();

            // Buat 2 dokumen pendukung untuk setiap kriteria
            for ($i = 1; $i <= 2; $i++) {
                $dokumenPendukung[] = [
                    'no_kriteria' => $kriteria->no_kriteria,
                    'id_user' => $randomUser->id_user, // Gunakan id_user dari user ANG
                    'nama_file' => 'contoh_' . $kriteria->no_kriteria . '_' . $i . '.pdf',
                    'path_file' => 'storage/dokumen/contoh_' . $kriteria->no_kriteria . '_' . $i . '.pdf',
                    'keterangan' => 'Dokumen pendukung ' . $i . ' untuk kriteria ' . $kriteria->no_kriteria . ' (Uploaded by User ' . $randomUser->id_user . ')',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('dokumen_pendukung')->insert($dokumenPendukung);
    }
}
