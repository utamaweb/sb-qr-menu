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
        Schema::create('close_cashier_product_solds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('close_cashier_id');
            $table->string('product_name');
            $table->bigInteger('qty');
            $table->bigInteger('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_cashier_product_solds');
    }
};
