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
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->date('scheduled_date')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->dropColumn('scheduled_date');
            $table->dropColumn('start');
            $table->dropColumn('end');
        });
    }
};
