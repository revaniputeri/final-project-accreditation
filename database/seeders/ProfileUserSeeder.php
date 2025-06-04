<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileUserSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID dan username (nidn) dari user level ADM, VAL, DIR
        $admin = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('kode_level', 'ADM')
            ->first();

        $validator = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('kode_level', 'VAL')
            ->first();

        $director = DB::table('user')
            ->join('level', 'user.id_level', '=', 'level.id_level')
            ->where('kode_level', 'DIR')
            ->first();

        // Admin profile (dummy)
        DB::table('profile_user')->insert([
            'id_user' => $admin->id_user,
            'nama_lengkap' => 'Rina Oktaviani',
            'tempat_tanggal_lahir' => 'Jakarta, 8 Agustus 1988',
            'nidn' => $admin->username,
            'nip' => '198808081234567800',
            'jabatan_fungsional' => 'Administrator',
            'pendidikan_terakhir' => 'S2',
            'pangkat' => '-',
            'no_telp' => '081234567800',
            'alamat' => 'Jl. Melati No. 12, Jakarta',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Validator profile (dummy)
        DB::table('profile_user')->insert([
            'id_user' => $validator->id_user,
            'nama_lengkap' => 'Ahmad Fajar Pratama',
            'tempat_tanggal_lahir' => 'Bandung, 12 Desember 1985',
            'nidn' => $validator->username,
            'nip' => '198512121234567801',
            'jabatan_fungsional' => 'Dosen Validator',
            'pendidikan_terakhir' => 'S2',
            'pangkat' => 'III/b',
            'no_telp' => '081234567801',
            'alamat' => 'Jl. Teratai No. 9, Bandung',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Director profile (dummy)
        DB::table('profile_user')->insert([
            'id_user' => $director->id_user,
            'nama_lengkap' => 'Prof. Dr. Surya Wijaya',
            'tempat_tanggal_lahir' => 'Surabaya, 20 Mei 1970',
            'nidn' => $director->username,
            'nip' => '197005201234567802',
            'gelar_depan' => 'Prof. Dr.',
            'gelar_belakang' => '',
            'jabatan_fungsional' => 'Direktur',
            'pendidikan_terakhir' => 'S3',
            'pangkat' => 'IV/c',
            'no_telp' => '081234567802',
            'alamat' => 'Jl. Cendana No. 1, Surabaya',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criteria staff profiles
        $dummyNamesStaff = [
            'Slamet Riyadi',
            'Rini Kusuma',
            'Tono Sutrisno',
            'Dewi Anggraini',
            'Budi Hartono',
            'Sari Melati',
            'Agus Santoso',
            'Nina Marlina',
            'Joko Prabowo',
        ];

        for ($i = 0; $i < 9; $i++) {
            // Ambil nidn dari username user level ANG
            $nidn = DB::table('user')
                ->where('id_level', DB::table('level')->where('kode_level', 'ANG')->value('id_level'))
                ->skip($i)
                ->first()
                ->username;

            DB::table('profile_user')->insert([
                'id_user' => DB::table('user')->where('username', $nidn)->first()->id_user,
                'nama_lengkap' => $dummyNamesStaff[$i],
                'tempat_tanggal_lahir' => 'Kota, ' . rand(1, 28) . ' ' . ['Januari', 'Februari', 'Maret'][rand(0, 2)] . ' ' . rand(1980, 1990),
                'nidn' => $nidn,
                'nip' => '199' . rand(0, 9) . rand(0, 9) . rand(0, 9) . '0101' . rand(1000, 9999),
                'jabatan_fungsional' => 'Staff Akademik',
                'pendidikan_terakhir' => 'S1',
                'pangkat' => 'III/a',
                'no_telp' => '08' . rand(100000000, 999999999),
                'alamat' => 'Jl. Staff No. ' . ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Lecturer profiles
        $dummyNamesDosen = [
            'Arif Santoso',
            'Lina Marlina',
            'Yusuf Hidayat',
            'Maya Sari',
            'Dedi Prasetyo',
            'Rina Wulandari',
            'Joko Saputra',
            'Fitri Anggraeni',
            'Agus Gunawan',
            'Sari Dewi',
        ];

        $gelar = ['S.T., M.T.', 'S.Kom., M.Kom.', 'S.Si., M.Si.'];
        $jabatan = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
        $pangkat = ['III/a', 'III/b', 'III/c', 'IV/a', 'IV/b'];

        for ($i = 0; $i < 10; $i++) {
            // Ambil nidn dari username user level DOS
            $nidn = DB::table('user')
                ->where('id_level', DB::table('level')->where('kode_level', 'DOS')->value('id_level'))
                ->skip($i)
                ->first()
                ->username;

            DB::table('profile_user')->insert([
                'id_user' => DB::table('user')->where('username', $nidn)->first()->id_user,
                'nama_lengkap' => $dummyNamesDosen[$i] . ' ' . $gelar[array_rand($gelar)],
                'tempat_tanggal_lahir' => 'Kota, ' . rand(1, 28) . ' ' . ['Januari', 'Februari', 'Maret'][rand(0, 2)] . ' ' . rand(1970, 1985),
                'nidn' => $nidn,
                'nip' => '19' . rand(70, 85) . rand(0, 9) . rand(0, 9) . '0101' . rand(1000, 9999),
                'gelar_depan' => (rand(0, 3) == 0 ? 'Dr.' : null),
                'gelar_belakang' => $gelar[array_rand($gelar)],
                'jabatan_fungsional' => $jabatan[array_rand($jabatan)],
                'pendidikan_terakhir' => (rand(0, 1) ? 'S2' : 'S3'),
                'pangkat' => $pangkat[array_rand($pangkat)],
                'no_telp' => '08' . rand(100000000, 999999999),
                'alamat' => 'Jl. Dosen No. ' . ($i + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
