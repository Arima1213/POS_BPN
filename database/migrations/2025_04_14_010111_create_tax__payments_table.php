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
        Schema::create('tax_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_id')->constrained('taxes')->onDelete('cascade');
            $table->decimal('amount_paid', 18, 2);
            $table->date('payment_date')->nullable();
            $table->string('tax_invoice_number')->nullable(); // Bukti bayar atau ID billing
            $table->string('payment_method')->nullable(); // Transfer, VA, dll
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax__payments');
    }
};
