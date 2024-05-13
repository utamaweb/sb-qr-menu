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
            // Section 1
            $table->date("date");
            $table->datetime("open_time");
            $table->datetime("close_time")->nullable();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->bigInteger("initial_balance")->nullable();
            $table->bigInteger("total_cash")->nullable();
            // Section 2
            $table->bigInteger("gofood_omzet")->default(0);
            $table->bigInteger("grabfood_omzet")->default(0);
            $table->bigInteger("shopeefood_omzet")->default(0);
            $table->bigInteger("qris_omzet")->default(0);
            $table->bigInteger("transfer_omzet")->default(0);
            $table->bigInteger("total_non_cash")->default(0);
            // Section 3
            $table->bigInteger("total_expense")->nullable(); // ini keknya perlu tabel lagi untuk detailkan pengeluarannya apa aja seperti (air galon, es batu, timun, dll)

            // Section 4
            $table->bigInteger("total_income")->nullable(); // total_cash - total_expense
            $table->bigInteger("cash_in_drawer")->nullable();
            $table->bigInteger("total_product_sales")->nullable();
            $table->bigInteger("auto_balance")->nullable();
            $table->bigInteger("calculated_balance")->nullable();
            $table->bigInteger("difference")->nullable();
            $table->boolean("is_closed")->default(0);
            $table->softDeletes();
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
