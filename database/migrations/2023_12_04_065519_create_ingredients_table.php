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
            // $table->bigInteger("first_stock")->nullable();
            // $table->bigInteger("stock_in")->default(0);
            // $table->bigInteger("stock_used")->default(0);
            // $table->bigInteger("adjustment")->default(0);
            // $table->bigInteger("last_stock")->default(0);
            // $table->bigInteger("max_stock")->default(0);
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
