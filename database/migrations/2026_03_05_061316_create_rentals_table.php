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
    Schema::create('rentals', function (Blueprint $table) {
        $table->id();
        $table->string('nama_unit'); // TV1 - TV5
        $table->string('nama_penyewa');
        $table->decimal('durasi', 5, 2); 
        $table->integer('tarif_per_jam')->default(5000);
        $table->integer('total_biaya');
        $table->dateTime('jam_mulai');
        $table->dateTime('jam_selesai');
        
        // Fitur Soft Delete & Audit Trail sesuai request
        $table->boolean('is_deleted')->default(false);
        $table->timestamp('tgl_hapus')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
