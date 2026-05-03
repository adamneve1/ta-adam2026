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
        Schema::create('pks_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pks_id')->constrained()->cascadeOnDelete();
    $table->foreignId('katalog_id')->constrained('katalogs');
    $table->string('waktu'); // regular / prime
    $table->string('channel'); // pro1 / pro2
    $table->integer('qty');
    $table->decimal('tarif', 15, 2);
    $table->decimal('subtotal', 15, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_items');
    }
};
