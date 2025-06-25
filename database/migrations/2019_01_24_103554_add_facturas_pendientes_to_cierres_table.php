<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacturasPendientesToCierresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cierres', function (Blueprint $table) {
            $table->integer('Facturas_pendientes')->default(0);
            $table->decimal('TotalVentasPendientes', 18, 5)->default(0);
            $table->decimal('TotalVentasPendientesDolar', 18, 5)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cierres', function (Blueprint $table) {
            $table->dropColumn('Facturas_pendientes');
            $table->dropColumn('TotalVentasPendientes');
            $table->dropColumn('TotalVentasPendientesDolar');
        });
    }
}
