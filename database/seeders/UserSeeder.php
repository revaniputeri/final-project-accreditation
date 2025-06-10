<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $adminNidn = (string) rand(1000000000, 9999999999);
        $adminId = DB::table('user')->insertGetId([
            'username' => $adminNidn,
            'password' => Hash::make($adminNidn),
            'id_level' => DB::table('level')->where('kode_level', 'ADM')->first()->id_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create validator user
        $validatorNidn = (string) rand(1000000000, 9999999999);
        $validatorId = DB::table('user')->insertGetId([
            'username' => $validatorNidn,
            'password' => Hash::make($validatorNidn),
            'id_level' => DB::table('level')->where('kode_level', 'VAL')->first()->id_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create director user
        $directorNidn = (string) rand(1000000000, 9999999999);
        $directorId = DB::table('user')->insertGetId([
            'username' => $directorNidn,
            'password' => Hash::make($directorNidn),
            'id_level' => DB::table('level')->where('kode_level', 'DIR')->first()->id_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create criteria staff (9 users)
        for ($i = 1; $i <= 9; $i++) {
            $nidn = (string) rand(1000000000, 9999999999);
            DB::table('user')->insert([
                'username' => $nidn,
                'password' => Hash::make($nidn),
                'id_level' => DB::table('level')->where('kode_level', 'ANG')->first()->id_level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create 10 lecturer accounts
        for ($i = 1; $i <= 10; $i++) {
            $nidn = (string) rand(1000000000, 9999999999);
            DB::table('user')->insert([
                'username' => $nidn,
                'password' => Hash::make($nidn),
                'id_level' => DB::table('level')->where('kode_level', 'DOS')->first()->id_level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
