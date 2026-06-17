<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['atasan', 'Kepala Stasiun', 'kepsta'])
            ->update(['role' => 'kepala_stasiun']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'kepala_stasiun')
            ->update(['role' => 'Kepala Stasiun']);
    }
};
