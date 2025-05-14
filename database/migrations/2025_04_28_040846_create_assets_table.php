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
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 20, 2);
            $table->date('purchase_date');
            $table->integer('useful_life_years');
            $table->decimal('residual_value', 20, 2)->default(0);
            $table->string('location')->nullable();
            $table->enum('category', ['vehicle', 'office_equipment', 'building', 'land', 'others']);
            $table->enum('status', ['active', 'sold', 'damaged', 'lost', 'retired'])->default('active');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->date('depreciation_start_date')->nullable();
            $table->boolean('is_fully_depreciated')->default(false);
            $table->enum('depreciation_method', ['straight_line', 'declining_balance'])->default('straight_line');
            $table->decimal('book_value', 20, 2)->default(0);
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
