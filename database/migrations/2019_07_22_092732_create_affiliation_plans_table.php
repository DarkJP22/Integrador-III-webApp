<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliationPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliation_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->unsigned()->index();
            $table->string('name');
            $table->decimal('cuota', 18, 5)->default(0);
            $table->decimal('discount', 4, 2)->default(0);
            $table->string('period');
            $table->integer('persons')->default(0);
            $table->string('CodigoMoneda')->default('CRC');
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
        Schema::dropIfExists('affiliation_plans');
    }
}
