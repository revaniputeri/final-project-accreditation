<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokumenPendukungSeeder extends Seeder
{
    public function run()
    {
        $kriteriaList = DB::table('kriteria')->get();
        $data = [];

        foreach ($kriteriaList as $kriteria) {
            for ($i = 1; $i <= 1; $i++) {
                $data[] = [
                    'no_kriteria' => $kriteria->no_kriteria,
                    'id_user' => $kriteria->id_user,
                    'nama_file' => "Contoh",
                    'path_file' => "contoh.pdf",
                    'keterangan' => "Dokumen pendukung {$i} untuk kriteria {$kriteria->no_kriteria}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('dokumen_pendukung')->insert($data);
    }
}
