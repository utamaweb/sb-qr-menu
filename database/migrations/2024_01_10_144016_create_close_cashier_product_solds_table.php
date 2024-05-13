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
            $table->foreignId('close_cashier_id')->constrained('close_cashiers', 'id')->onDelete('cascade');
            $table->string('product_name');
            $table->bigInteger('qty');
            $table->softDeletes();
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
