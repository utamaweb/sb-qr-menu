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
        Schema::create('ojol_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ojol_id')->constrained('ojols', 'id')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses', 'id')->onDelete('cascade');
            $table->integer('percent')->nullable();
            $table->integer('extra_price')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ojol_warehouses');
    }
};
