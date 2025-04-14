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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_type'); // PPN, PPh 21, PBB, dll
            $table->string('tax_period'); // Format: YYYY-MM
            $table->string('npwp')->nullable();
            $table->integer('amount_due'); // julah pajak
            $table->integer('amount_paid'); // jumlah yang sudah dibayar
            $table->enum('status', ['Belum Dibayar', 'Sebagian Dibayar', 'Lunas', 'Nunggak'])->default('Belum Dibayar');
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};