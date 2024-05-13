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
        Schema::create('ojol_close_cashiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ojol_id')->constrained('ojols', 'id')->onDelete('cascade');
            $table->foreignId('close_cashier_id')->constrained('close_cashiers', 'id')->onDelete('cascade');
            $table->bigInteger('omzet');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ojol_close_cashiers');
    }
};
