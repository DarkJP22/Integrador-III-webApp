<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('api_token',100)->nullable();
            $table->string('phone_number');
            $table->string('phone_country_code');
            $table->tinyInteger('active')->default(1);
            $table->string('provider')->default('email');
            $table->string('provider_id')->unique();
            $table->float('rating_service_cache', 2, 1)->unsigned()->default(3.0);
            $table->float('rating_medic_cache', 2, 1)->unsigned()->default(3.0);
            $table->float('rating_app_cache', 2, 1)->unsigned()->default(3.0);
            $table->integer('rating_service_count')->unsigned()->default(0);
            $table->integer('rating_medic_count')->unsigned()->default(0);
            $table->integer('rating_app_count')->unsigned()->default(0);
            $table->double('commission')->default(0);
            $table->string('medic_code')->nullable();
            $table->string('ide')->nullable();
            $table->tinyInteger('fe')->default(0); //utiliza factura electronica
            $table->string('push_token')->nullable();
            $table->tinyInteger('pharmacy_notifications')->default(1); //si permitir el envio de notificaciones push
            $table->tinyInteger('clinic_notifications')->default(1); //si permitir el envio de notificaciones push
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
