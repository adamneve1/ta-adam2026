<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@rribatam.com'],
            [
                'name' => 'Admin Sistem',
                'nip' => null,
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'akunkepsta@rribatam.com'],
            [
                'name' => 'Suhendra, SE ',
                'nip' => '197204121998031002',
                'password' => Hash::make('password'),
                'role' => 'kepala_stasiun',
            ]
        );

        User::updateOrCreate(
            ['email' => 'lpu@rribatam.com'],
            [
                'name' => 'LPU',
                'nip' => '198606182010121004',
                'password' => Hash::make('password'),
                'role' => 'lpu',
            ]
        );

        User::updateOrCreate(
            ['email' => 'penyetor@rribatam.com'],
            [
                'name' => 'Penyetor',
                'nip' => '199205072019031008',
                'password' => Hash::make('password'),
                'role' => 'penyetor',
            ]
        );

        $this->call([
            KatalogSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
