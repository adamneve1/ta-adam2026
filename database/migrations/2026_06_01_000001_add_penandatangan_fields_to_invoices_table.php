<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('penyetor_nama')->nullable()->after('kode_billing');
            $table->string('penyetor_nip', 30)->nullable()->after('penyetor_nama');
            $table->string('kepala_stasiun_nama')->nullable()->after('penyetor_nip');
            $table->string('kepala_stasiun_nip', 30)->nullable()->after('kepala_stasiun_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'penyetor_nama',
                'penyetor_nip',
                'kepala_stasiun_nama',
                'kepala_stasiun_nip',
            ]);
        });
    }
};
