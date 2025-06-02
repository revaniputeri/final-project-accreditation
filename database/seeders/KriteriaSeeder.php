<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KriteriaSeeder extends Seeder
{
    public function run()
    {
        $kriteriaData = [];

        // Ambil semua user dengan role ANG
        $angUsers = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'ANG')
            ->get();

        Log::info('KriteriaSeeder: Found ' . count($angUsers) . ' users with role ANG.');

        foreach ($angUsers as $user) {
            // Buat 5 kriteria untuk setiap user (no_kriteria 1-5)
            for ($noKriteria = 1; $noKriteria <= 5; $noKriteria++) {
                $kriteriaData[] = [
                    'no_kriteria' => $noKriteria,
                    'id_user' => $user->id_user,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert data kriteria
        DB::table('kriteria')->insert($kriteriaData);
    }
}
