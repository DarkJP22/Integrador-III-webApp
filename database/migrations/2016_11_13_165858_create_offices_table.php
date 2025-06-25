<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('name');
            $table->string('address');
            $table->string('province');
            $table->string('canton');
            $table->string('district');
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('ide')->nullable();
            $table->string('ide_name')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->tinyInteger('notification')->default(0);
            $table->dateTime('notification_date')->nullable();
            $table->string('address_map')->nullable();
            $table->string('logo_path')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->char('bill_to', 2)->default('C');
            $table->tinyInteger('fe')->default(0);
            $table->timestamps();
        });

         Schema::create('office_user', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('office_id')->unsigned()->index();
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('verified')->default(0);
            $table->char('obligado_tributario', 2)->default('M');
            
        });

        Schema::create('currency_office', function (Blueprint $table) {
            $table->unsignedInteger('currency_id')->index();
            $table->unsignedInteger('office_id')->index();

            $table->unique(['currency_id', 'office_id']);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_office');
        Schema::dropIfExists('office_user');
        Schema::dropIfExists('offices');
    }
}
