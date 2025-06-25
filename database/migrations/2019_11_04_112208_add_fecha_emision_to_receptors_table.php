<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaEmisionToReceptorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receptors', function (Blueprint $table) {
            $table->timestamp('FechaEmisionFactura')->after('MontoTotalDeGastoAplicable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receptors', function (Blueprint $table) {
            $table->dropColumn('FechaEmisionFactura');
        });
    }
}
