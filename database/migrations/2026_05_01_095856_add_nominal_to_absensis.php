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
        Schema::table('absensis', function (Blueprint $table) {
            $table->decimal('nominal_basic', 15, 2)->nullable();
            $table->decimal('nominal_lembur', 15, 2)->nullable();
            $table->decimal('nominal_makan', 15, 2)->nullable();
            $table->decimal('nominal_kasbon', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['nominal_basic', 'nominal_lembur', 'nominal_makan', 'nominal_kasbon']);
        });
    }
};
