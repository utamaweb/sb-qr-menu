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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->bigInteger("first_stock");
            $table->bigInteger("stock_in");
            $table->bigInteger("stock_used");
            $table->bigInteger("adjustment");
            $table->bigInteger("last_stock");
            $table->unsignedBigInteger('unit_id');
            // $table->foreignId('unit_id')->constrained();
            $table->timestamps();

            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
