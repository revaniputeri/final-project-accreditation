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
        $adminId = DB::table('user')->insertGetId([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'id_level' => DB::table('level')->where('kode_level', 'ADM')->first()->id_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create validator user
        $validatorId = DB::table('user')->insertGetId([
            'username' => 'validator',
            'password' => Hash::make('password'),
            'id_level' => DB::table('level')->where('kode_level', 'VAL')->first()->id_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create director user
        $directorId = DB::table('user')->insertGetId([
            'username' => 'direktur',
            'password' => Hash::make('password'),
            'id_level' => DB::table('level')->where('kode_level', 'DIR')->first()->id_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create criteria staff (9 users)
        for ($i = 1; $i <= 9; $i++) {
            $nidn = '000000' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            DB::table('user')->insert([
                'username' => $nidn,
                'password' => Hash::make('password'),
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
                'password' => Hash::make('password'),
                'id_level' => DB::table('level')->where('kode_level', 'DOS')->first()->id_level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
