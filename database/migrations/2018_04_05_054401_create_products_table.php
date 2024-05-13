<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('code');
            $table->string('type');
            $table->string('barcode_symbology')->nullable();
            // $table->unsignedBigInteger('category_id');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'id')->onDelete('cascade');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            // $table->integer('purchase_unit_id')->nullable();
            // $table->integer('sale_unit_id')->nullable();
            $table->double('cost')->nullable();
            $table->double('price')->nullable();
            $table->double('qty')->nullable();
            $table->double('alert_quantity')->nullable();
            // $table->tinyInteger('promotion')->nullable();
            // $table->string('promotion_price')->nullable();
            // $table->date('starting_date')->nullable();
            // $table->date('last_date')->nullable();
            // $table->integer('tax_id')->nullable();
            // $table->integer('tax_method')->nullable();
            $table->longText('image')->nullable();
            // $table->tinyInteger('featured')->nullable();
            $table->text('product_details')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('is_diffPrice')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
