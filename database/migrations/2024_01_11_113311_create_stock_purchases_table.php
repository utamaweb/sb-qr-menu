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
            $table->foreignId('warehouse_id')->constrained('warehouses', 'id')->onDelete('cascade');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
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
