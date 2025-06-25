<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code', 2)->default('01');
            $table->string('name');
            $table->decimal('tarifa', 4, 2);
            $table->timestamps();
        });

        Schema::create('product_tax', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->index();
            $table->unsignedInteger('tax_id')->index();

            $table->unique(['product_id', 'tax_id']);
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     
        Schema::dropIfExists('product_tax');
        Schema::dropIfExists('taxes');
    }
}
