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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->enum('kelompok', ['aset', 'kewajiban', 'ekuitas', 'pendapatan', 'beban']);
            $table->enum('tipe', ['lancar', 'tetap', 'jangka_pendek', 'jangka_panjang', 'modal', 'operasional', 'non_operasional']);
            $table->enum('jenis_beban', ['beban_kas', 'beban_non_kas', 'beban_usaha', 'beban_operasional', 'beban_lainnya'])->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};