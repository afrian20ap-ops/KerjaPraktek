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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('status')->default('Hadir'); // Hadir, Terlambat, Sakit, Izin, Alpa
            $table->decimal('total_hari', 3, 1)->default(1.0); // 1.0 = normal, 1.5 = sunday/holiday
            $table->integer('jam_lembur')->default(0); // dalam jam
            $table->boolean('dapat_uang_makan')->default(true); // false jika hanya setengah hari dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
