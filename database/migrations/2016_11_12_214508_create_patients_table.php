<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ide')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('birth_date');
            $table->string('gender', 2);
            $table->string('phone_number');
            $table->string('phone_country_code');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('province');
            $table->string('city')->nullable();
            $table->string('avatar_path')->nullable();
            $table->text('conditions')->nullable();
            $table->integer('created_by')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::create('patient_user', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('patient_id')->unsigned()->index();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('authorization')->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_user');
        Schema::dropIfExists('patients');
    }
}
