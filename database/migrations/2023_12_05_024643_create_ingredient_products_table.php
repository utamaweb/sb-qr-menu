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
        Schema::create('ingredient_products', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('ingredient_id');
            // $table->unsignedBigInteger('product_id');
            $table->foreignId('ingredient_id');
            $table->foreignId('product_id');
            $table->softDeletes();
            $table->timestamps();

            // $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_products');
    }
};
