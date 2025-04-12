<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2); // Jumlah hutang
            $table->decimal('paid', 12, 2)->default(0); // Jumlah yang sudah dibayar
            $table->text('note')->nullable(); // Catatan opsional
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};