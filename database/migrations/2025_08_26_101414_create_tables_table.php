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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment("System generated code");
            $table->string('name')->comment("User defined name");
            $table->foreignId('outlet_id')->constrained('warehouses', 'id')->onDelete('cascade');

            // Define index
            $table->index('code');
            $table->index(['code', 'outlet_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
