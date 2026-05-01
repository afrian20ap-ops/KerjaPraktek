<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_lapangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Supervisi yang buat
            $table->date('tanggal');
            $table->unsignedTinyInteger('minggu_ke')->nullable(); // nomor minggu dalam setahun
            $table->string('lokasi')->nullable();
            $table->text('deskripsi_pekerjaan');
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            $table->string('foto_path')->nullable();
            $table->enum('status', ['Draft', 'Terkirim', 'Disetujui'])->default('Terkirim');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_lapangans');
    }
};
