<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptreatmentPatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('optreatment_patient', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->unsigned()->index();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('optreatment_id')->index();
            $table->foreign('optreatment_id')->references('id')->on('optreatments')->onDelete('cascade');
            $table->integer('appointment_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('optreatment_id')->nullable()->index();
            $table->boolean('is_esthetic')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('optreatment_id');
            $table->dropColumn('is_esthetic');
        });
        Schema::dropIfExists('optreatment_patient');
    }
}
