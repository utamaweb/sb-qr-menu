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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->bigInteger('first_stock')->default(0);
            $table->bigInteger('stock_in')->default(0);
            $table->bigInteger('stock_used')->default(0);
            $table->bigInteger('last_stock')->default(0);
            // $table->bigInteger('id_shift_in'); ?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
