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
        Schema::create('stock_purchase_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_purchase_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->string('qty');
            $table->string('notes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_purchase_ingredients');
    }
};
