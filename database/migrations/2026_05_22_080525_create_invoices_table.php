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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pks_id')->constrained('pks')->cascadeOnDelete();
            $table->string('nomor_invoice')->unique();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_invoice');
            $table->date('tanggal_jatuh_tempo');
            $table->string('status')->default('Belum_Bayar'); // unpaid, paid
            $table->string('kode_billing')->nullable(); // SIMPONI billing code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
