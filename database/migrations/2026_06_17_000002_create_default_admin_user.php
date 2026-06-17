<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('users')->where('email', 'admin@rribatam.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Admin Sistem',
                'email' => 'admin@rribatam.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@rribatam.com')
            ->delete();
    }
};
