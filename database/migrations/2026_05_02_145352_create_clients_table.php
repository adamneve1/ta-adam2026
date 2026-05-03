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
      Schema::create('clients', function (Blueprint $table) {
    $table->id();

    $table->enum('jenis_klien', [
    'Instansi Pemerintahan',
    'Perusahaan Swasta',
    'BUMN',
    'BUMD',
    'Lembaga',
    'Organisasi Nirlaba',
    'Perorangan'
]);
    $table->string('nama');

    // Narahubung
    $table->string('nama_narahubung')->nullable();
    $table->string('no_narahubung')->nullable();
    $table->string('email')->nullable();

    // Penanggung jawab
    $table->string('nama_penanggung_jawab')->nullable();
    $table->string('jabatan')->nullable();

    // Tambahan
    $table->string('agen_rri')->nullable();
    $table->text('alamat')->nullable();
    $table->text('catatan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
