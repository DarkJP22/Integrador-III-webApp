<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailablesForToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->tinyInteger('for_medic')->default(0);
            $table->tinyInteger('for_clinic')->default(0);
            $table->tinyInteger('for_pharmacy')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('for_medic');
            $table->dropColumn('for_clinic');
            $table->dropColumn('for_pharmacy');
        });
    }
}
