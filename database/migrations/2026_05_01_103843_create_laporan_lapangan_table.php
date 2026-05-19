<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->date('tanggal');
            $table->unsignedTinyInteger('minggu_ke')->nullable(); 
            $table->string('lokasi')->nullable();
            $table->text('deskripsi_pekerjaan')->nullable();
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            $table->text('foto_paths')->nullable();
            $table->text('foto_deskripsis')->nullable();
            $table->enum('status', ['Draft', 'Terkirim', 'Disetujui', 'Ditolak'])->default('Terkirim');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_lapangan');
    }
};
