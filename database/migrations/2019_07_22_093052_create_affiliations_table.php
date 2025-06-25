<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->unsigned()->index();
            $table->integer('affiliation_plan_id')->unsigned()->index();
            $table->integer('patient_id')->unsigned()->index();
            $table->dateTime('inscription');
            $table->decimal('acumulado', 18, 5)->default(0);
            $table->string('CodigoMoneda')->default('CRC');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('affiliation_patient', function (Blueprint $table) {
            $table->unsignedInteger('affiliation_id')->index();
            $table->unsignedInteger('patient_id')->index();

            $table->unique(['affiliation_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliation_patient');
        Schema::dropIfExists('affiliations');
    }
}
