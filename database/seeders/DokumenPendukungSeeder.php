<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DokumenPendukungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all dokumen_kriteria
        $kriterias = DB::table('dokumen_kriteria')->get();

        foreach ($kriterias as $kriteria) {
            DB::table('dokumen_pendukung')->insert([
                'id_dokumen_kriteria' => $kriteria->id_dokumen_kriteria,
                'nama_file' => 'contoh.pdf',
                'path_file' => 'contoh.pdf',
                'keterangan' => 'Dokumen pendukung untuk kriteria ' . $kriteria->no_kriteria,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
