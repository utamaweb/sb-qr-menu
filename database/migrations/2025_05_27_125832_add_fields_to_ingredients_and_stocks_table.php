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
        // add minimum_stock field to ingredients table
        Schema::table('ingredients', function (Blueprint $table) {
            $table->integer('minimum_stock')->default(0)->after('name');
        });

        // add broken_stock field to stocks table
        Schema::table('stocks', function (Blueprint $table) {
            $table->bigInteger('broken_stock')->default(0)->after('stock_used');
        });

        // add regional_id field to warehouses table
        Schema::table('warehouses', function (Blueprint $table) {
            $table->unsignedBigInteger('regional_id')->nullable()->after('is_whatsapp_active');
            $table->foreign('regional_id')->references('id')->on('regionals')->onDelete('set null');
        });
    }
};
