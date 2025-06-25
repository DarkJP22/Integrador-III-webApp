<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayColumnsToAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('cost', 18, 5)->default(0);
            $table->decimal('available_accumulated_discount', 18, 5)->default(0);
            $table->decimal('total_cost', 18, 5)->default(0);
            $table->char('CodigoMoneda', 3)->default('CRC');
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
            $table->dropColumn('cost');
            $table->dropColumn('available_accumulated_discount');
            $table->dropColumn('total_cost');
            $table->dropColumn('CodigoMoneda');
        });
    }
}
