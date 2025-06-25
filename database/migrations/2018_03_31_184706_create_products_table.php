<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('office_id')->unsigned()->index();
            $table->char('type', 2)->default('S');
            $table->string('code')->nullable();
            $table->string('name');
            $table->decimal('quantity', 8, 2)->default(0);
            $table->double('price');
            $table->double('priceWithTaxes')->default(0);
            $table->double('taxesAmount')->default(0);
            $table->tinyInteger('exo')->default(0);
            $table->string('measure')->default('Unid');
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
