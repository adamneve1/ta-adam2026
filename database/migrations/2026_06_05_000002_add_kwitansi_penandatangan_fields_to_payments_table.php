<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('kwitansi_penyetor_nama')->nullable()->after('bukti_pembayaran_path');
            $table->string('kwitansi_penyetor_nip', 30)->nullable()->after('kwitansi_penyetor_nama');
            $table->string('kwitansi_kepala_stasiun_nama')->nullable()->after('kwitansi_penyetor_nip');
            $table->string('kwitansi_kepala_stasiun_nip', 30)->nullable()->after('kwitansi_kepala_stasiun_nama');
        });

        DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->select([
                'payments.id',
                'invoices.penyetor_nama',
                'invoices.penyetor_nip',
                'invoices.kepala_stasiun_nama',
                'invoices.kepala_stasiun_nip',
            ])
            ->orderBy('payments.id')
            ->get()
            ->each(function ($payment) {
                DB::table('payments')
                    ->where('id', $payment->id)
                    ->update([
                        'kwitansi_penyetor_nama' => $payment->penyetor_nama,
                        'kwitansi_penyetor_nip' => $payment->penyetor_nip,
                        'kwitansi_kepala_stasiun_nama' => $payment->kepala_stasiun_nama,
                        'kwitansi_kepala_stasiun_nip' => $payment->kepala_stasiun_nip,
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'kwitansi_penyetor_nama',
                'kwitansi_penyetor_nip',
                'kwitansi_kepala_stasiun_nama',
                'kwitansi_kepala_stasiun_nip',
            ]);
        });
    }
};
