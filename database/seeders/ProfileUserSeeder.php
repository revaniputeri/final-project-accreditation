<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileUserSeeder extends Seeder
{
    public function run()
    {
        // Admin profile
        DB::table('profile_user')->insert([
            'id_user' => DB::table('user')->where('username', 'admin')->first()->id_user,
            'nama_lengkap' => 'Admin Sistem',
            'tempat_tanggal_lahir' => 'Jakarta, 1 Januari 1980',
            'nidn' => '0000000000',
            'nip' => '198001011234567890',
            'jabatan_fungsional' => 'Administrator',
            'pendidikan_terakhir' => 'S2',
            'pangkat' => '-',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Admin No. 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Validator profile
        DB::table('profile_user')->insert([
            'id_user' => DB::table('user')->where('username', 'validator')->first()->id_user,
            'nama_lengkap' => 'Tim Validator',
            'tempat_tanggal_lahir' => 'Bandung, 15 Februari 1985',
            'nidn' => '0000000001',
            'nip' => '198502151234567891',
            'jabatan_fungsional' => 'Validator',
            'pendidikan_terakhir' => 'S2',
            'pangkat' => 'III/c',
            'no_telp' => '081234567891',
            'alamat' => 'Jl. Validator No. 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Director profile
        DB::table('profile_user')->insert([
            'id_user' => DB::table('user')->where('username', 'direktur')->first()->id_user,
            'nama_lengkap' => 'Dr. Bambang S.T., M.T.',
            'tempat_tanggal_lahir' => 'Surabaya, 10 Maret 1975',
            'nidn' => '0000000002',
            'nip' => '197503101234567892',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'S.T., M.T.',
            'jabatan_fungsional' => 'Direktur',
            'pendidikan_terakhir' => 'S3',
            'pangkat' => 'IV/a',
            'no_telp' => '081234567892',
            'alamat' => 'Jl. Direktur No. 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criteria staff profiles
        for ($i = 1; $i <= 9; $i++) {
            DB::table('profile_user')->insert([
                'id_user' => DB::table('user')->where('username', 'kriteria' . $i)->first()->id_user,
                'nama_lengkap' => 'Staff Kriteria ' . $i,
                'tempat_tanggal_lahir' => 'Kota, ' . rand(1, 28) . ' ' . ['Januari', 'Februari', 'Maret'][rand(0, 2)] . ' ' . rand(1980, 1990),
                'nidn' => '0000000' . (100 + $i),
                'nip' => '199' . rand(0, 9) . rand(0, 9) . rand(0, 9) . '0101' . rand(1000, 9999),
                'jabatan_fungsional' => 'Staff Akademik',
                'pendidikan_terakhir' => 'S1',
                'pangkat' => 'III/a',
                'no_telp' => '08' . rand(100000000, 999999999),
                'alamat' => 'Jl. Staff No. ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Lecturer profiles
        $gelar = ['S.T., M.T.', 'S.Kom., M.Kom.', 'S.Si., M.Si.'];
        $jabatan = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
        $pangkat = ['III/a', 'III/b', 'III/c', 'IV/a', 'IV/b'];

        // Daftar nama asli
        $namaAsli = [
            'Andi Setiawan',
            'Rina Kurniawati',
            'Budi Santoso',
            'Dewi Lestari',
            'Fajar Nugroho',
            'Siti Nurhaliza',
            'Agus Prasetyo',
            'Nina Kartika',
            'Joko Subagyo',
            'Maya Wulandari'
        ];

        for ($i = 1; $i <= 10; $i++) {
            DB::table('profile_user')->insert([
                'id_user' => DB::table('user')->where('username', 'dosen' . $i)->first()->id_user,
                'nama_lengkap' => $namaAsli[$i - 1] . ' ' . $gelar[array_rand($gelar)],
                'tempat_tanggal_lahir' => 'Kota, ' . rand(1, 28) . ' ' . ['Januari', 'Februari', 'Maret'][rand(0, 2)] . ' ' . rand(1970, 1985),
                'nidn' => rand(1000000000, 9999999999),
                'nip' => '19' . rand(70, 85) . rand(0, 9) . rand(0, 9) . '0101' . rand(1000, 9999),
                'gelar_depan' => (rand(0, 3) == 0 ? 'Dr.' : null),
                'gelar_belakang' => $gelar[array_rand($gelar)],
                'jabatan_fungsional' => $jabatan[array_rand($jabatan)],
                'pendidikan_terakhir' => (rand(0, 1) ? 'S2' : 'S3'),
                'pangkat' => $pangkat[array_rand($pangkat)],
                'no_telp' => '08' . rand(100000000, 999999999),
                'alamat' => 'Jl. Dosen No. ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
