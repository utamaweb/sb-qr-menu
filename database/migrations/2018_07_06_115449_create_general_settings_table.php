<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title');
            $table->string('site_logo')->nullable();
            $table->boolean('is_rtl')->nullable();
            $table->string('currency');
            // $table->integer('package_id')->after('currency')->nullable();
            $table->string('staff_access');
            $table->string("without_stock")->default("no");
            $table->string('date_format');
            $table->string('developed_by')->nullable();
            $table->string('invoice_format')->nullable();
            $table->integer('decimal')->nullable()->default(2);
            $table->integer('state')->nullable();
            $table->string('theme');
            // $table->json("modules")->>nullable();
            $table->timestamps();
            $table->string('currency_position');
            $table->date('expiry_date')->nullable();
            // $table->boolean('is_zatca')->nullable();
            $table->string('company_name')->nullable();
            // $table->string('vat_registration_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
}
