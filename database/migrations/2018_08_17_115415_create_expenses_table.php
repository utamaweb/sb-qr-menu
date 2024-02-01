<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            // $table->string('reference_no')->nullable();
            // $table->string('name')->nullable();
            $table->string('qty')->nullable();
            $table->double('amount');
            $table->text('note')->nullable();
            $table->foreignId('expense_category_id');
            $table->foreignId('warehouse_id');
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
