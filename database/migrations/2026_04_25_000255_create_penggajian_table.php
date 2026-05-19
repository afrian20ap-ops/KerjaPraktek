<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('periode_mulai');
            $table->date('periode_akhir');
            $table->decimal('total_kehadiran_hari', 5, 1)->default(0);
            $table->integer('total_jam_lembur')->default(0);
            $table->integer('total_gaji_pokok')->default(0);
            $table->integer('total_uang_lembur')->default(0);
            $table->integer('total_uang_makan')->default(0);
            $table->integer('kasbon')->default(0);
            $table->integer('total_gaji_bersih')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
