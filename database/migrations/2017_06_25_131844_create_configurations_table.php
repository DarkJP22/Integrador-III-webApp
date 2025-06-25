<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount_attended')->default('1');
            $table->double('amount_expedient')->default('10');
            $table->tinyInteger('trial_for_new_registers')->default(1);
            $table->string('call_center')->nullable();
            $table->string('url_app_pacientes_android')->nullable();
            $table->string('url_app_pacientes_ios')->nullable();
            $table->string('url_app_medicos_android')->nullable();
            $table->string('url_app_medicos_ios')->nullable();
            
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
