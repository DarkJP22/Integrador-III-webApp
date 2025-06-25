<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDosisToMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->text('receta')->nullable();
        });
        Schema::table('pmedicines', function (Blueprint $table) {
            $table->text('receta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('receta');
        });
        Schema::table('pmedicines', function (Blueprint $table) {
            $table->dropColumn('receta');
        });
    }
}
