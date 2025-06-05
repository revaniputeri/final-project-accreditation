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
            $data[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'id_user' => $kriteria->id_user,
                'kategori' => 'penetapan',
                'nama_file' => "Contoh",
                'path_file' => "contoh.pdf",
                'keterangan' => "Dokumen pendukung {$kriteria->no_kriteria} untuk kriteria {$kriteria->no_kriteria}",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'id_user' => $kriteria->id_user,
                'kategori' => 'pelaksanaan',
                'nama_file' => "Contoh",
                'path_file' => "contoh.pdf",
                'keterangan' => "Dokumen pendukung {$kriteria->no_kriteria} untuk kriteria {$kriteria->no_kriteria}",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'id_user' => $kriteria->id_user,
                'kategori' => 'evaluasi',
                'nama_file' => "Contoh",
                'path_file' => "contoh.pdf",
                'keterangan' => "Dokumen pendukung {$kriteria->no_kriteria} untuk kriteria {$kriteria->no_kriteria}",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'id_user' => $kriteria->id_user,
                'kategori' => 'pengendalian',
                'nama_file' => "Contoh",
                'path_file' => "contoh.pdf",
                'keterangan' => "Dokumen pendukung {$kriteria->no_kriteria} untuk kriteria {$kriteria->no_kriteria}",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'no_kriteria' => $kriteria->no_kriteria,
                'id_user' => $kriteria->id_user,
                'kategori' => 'peningkatan',
                'nama_file' => "Contoh",
                'path_file' => "contoh.pdf",
                'keterangan' => "Dokumen pendukung {$kriteria->no_kriteria} untuk kriteria {$kriteria->no_kriteria}",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('dokumen_pendukung')->insert($data);
    }
}
