<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('level.kode_level', 'ANG')
            ->select('user.id_user')
            ->get();

        $maxUserPerKriteria = 2;
        $jumlahKriteria = 2;
        $kriteriaData = [];

        $userIndex = 0;

        for ($noKriteria = 1; $noKriteria <= $jumlahKriteria; $noKriteria++) {
            for ($i = 0; $i < $maxUserPerKriteria; $i++) {
                if ($userIndex >= count($users)) {
                    break 2;
                }

                $user = $users[$userIndex];
                $userIndex++;

                $kriteriaData[] = [
                    'no_kriteria' => $noKriteria,
                    'id_user' => $user->id_user,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('kriteria')->insert($kriteriaData);
    }
}
