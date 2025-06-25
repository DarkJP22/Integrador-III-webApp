<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicineRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmedicines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned()->index();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->integer('pharmacy_id')->unsigned()->index();
            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->string('name');
            $table->dateTime('date_purchase')->nullable();
            $table->tinyInteger('remember')->default(0); //0 no vista desde el panel de notificaciones
            $table->integer('remember_days')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::create('medicine_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('medicine_id')->index();
            $table->unsignedInteger('pharmacy_id')->index();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('medicine_reminder_patient', function (Blueprint $table) {
            $table->unsignedInteger('medicine_reminder_id')->index();
            $table->unsignedInteger('patient_id')->index();

            $table->unique(['medicine_reminder_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pmedicines');
        Schema::dropIfExists('medicine_reminder_patient');
        Schema::dropIfExists('medicine_reminders');
    }
}
