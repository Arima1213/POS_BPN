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
        Schema::create('show_product_landings', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['produk', 'jasa']);
            $table->enum('status', ['aktif', 'tidak']);
            $table->string('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('show_product_landings');
    }
};