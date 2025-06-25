<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('patient_id')->nullable()->index();
            $table->foreignId('office_id')->nullable()->index();
            $table->dateTime('appointment_date')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('patient_ide');
            $table->string('patient_name');
            $table->string('phone_number');
            $table->string('province');
            $table->string('canton');
            $table->string('district');
            $table->string('coords');
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
        Schema::dropIfExists('lab_appointment_requests');
    }
};
