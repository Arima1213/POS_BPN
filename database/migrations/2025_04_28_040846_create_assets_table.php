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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_name');
            $table->string('asset_code')->unique();
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 20, 2);
            $table->date('purchase_date');
            $table->integer('useful_life_years');
            $table->decimal('residual_value', 20, 2)->default(0);
            $table->string('location')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('journal_entry_id')->nullable(); // Relasi ke Journal Entry
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};