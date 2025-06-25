<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptreatmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('optreatments', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('name');
            $table->integer('product_id')->unsigned()->nullable()->index();
            $table->char('CodigoMoneda', 3)->default('CRC');
            $table->decimal('price', 18, 5)->default(0);
            $table->decimal('discount', 4, 2)->default(0);
            $table->decimal('tax', 4, 2)->default(0);
            $table->integer('office_id')->unsigned()->index();
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
        Schema::dropIfExists('optreatments');
    }
}
