<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            LevelSeeder::class,
            DokumenKriteriaSeeder::class,
            UserSeeder::class,
            ProfileUserSeeder::class,
            PSertifikasiSeeder::class,
            PKegiatanSeeder::class,
            PPrestasiSeeder::class,
            POrganisasiSeeder::class,
            PPublikasiSeeder::class,
            PPenelitianSeeder::class,
            PKaryaBukuSeeder::class,
            PHKISeeder::class,
            PPengabdianSeeder::class,
            PProfesiSeeder::class,
        ]);
    }
}
