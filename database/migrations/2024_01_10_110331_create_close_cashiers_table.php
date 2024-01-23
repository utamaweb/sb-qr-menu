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
        Schema::create('close_cashiers', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->datetime("open_time");
            $table->datetime("close_time")->nullable();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("warehouse_id");
            $table->bigInteger("initial_balance")->nullable();
            $table->bigInteger("total_cash")->nullable();
            $table->bigInteger("total_non_cash")->nullable();
            $table->bigInteger("total_income")->nullable();
            $table->bigInteger("total_expense")->nullable();
            $table->bigInteger("total_product_sales")->nullable();
            $table->bigInteger("auto_balance")->nullable();
            $table->bigInteger("calculated_balance")->nullable();
            $table->bigInteger("difference")->nullable();
            $table->boolean("is_closed")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_cashiers');
    }
};
