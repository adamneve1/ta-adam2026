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
    ['email' => 'akunkepsta@rribatam.com'],
    [
        'name' => 'Atasan',
        'password' => Hash::make('password'),
        'role' => 'atasan',
    ]
);

User::updateOrCreate(
    ['email' => 'lpu@rribatam.com'],
    [
        'name' => 'LPU',
        'password' => Hash::make('password'),
        'role' => 'lpu',
    ]
);

User::updateOrCreate(
    ['email' => 'penyetor@rribatam.com'],
    [
        'name' => 'Penyetor',
        'password' => Hash::make('password'),
        'role' => 'penyetor',
    ]
);
    }
}