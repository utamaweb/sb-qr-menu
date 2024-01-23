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
        Schema::create('stock_purchases', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->string("total_qty");
            $table->string("total_price")->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('close_cashier_id')->nullable();
            $table->foreign('close_cashier_id')->references('id')->on('close_cashiers')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_purchases');
    }
};
