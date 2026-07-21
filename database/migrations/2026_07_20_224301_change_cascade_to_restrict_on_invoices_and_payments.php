<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mengubah foreign key dari cascadeOnDelete() menjadi restrictOnDelete()
     * agar database menolak penghapusan PKS yang masih memiliki invoice,
     * dan menolak penghapusan invoice yang masih memiliki payment.
     */
    public function up(): void
    {
        // 1. invoices.pks_id: CASCADE → RESTRICT
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['pks_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('pks_id')
                  ->references('id')
                  ->on('pks')
                  ->restrictOnDelete();
        });

        // 2. payments.invoice_id: CASCADE → RESTRICT
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Mengembalikan ke cascadeOnDelete() (kondisi semula).
     */
    public function down(): void
    {
        // 1. invoices.pks_id: RESTRICT → CASCADE
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['pks_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('pks_id')
                  ->references('id')
                  ->on('pks')
                  ->cascadeOnDelete();
        });

        // 2. payments.invoice_id: RESTRICT → CASCADE
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->cascadeOnDelete();
        });
    }
};
