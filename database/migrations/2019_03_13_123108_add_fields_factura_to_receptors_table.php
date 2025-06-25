<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsFacturaToReceptorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receptors', function (Blueprint $table) {
            $table->string('nombre_emisor')->nullable();
            $table->string('email_emisor')->nullable();
            $table->char('TipoDocumento')->nullable()->default('01'); //
            $table->char('MedioPago', 2)->nullable()->default('01');
            $table->char('CondicionVenta',2 )->nullable()->default('01');
            $table->string('PlazoCredito')->nullable();
            $table->char('CodigoMoneda',3 )->nullable()->default('CRC'); //
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
            $table->dropColumn('nombre_emisor');
            $table->dropColumn('email_emisor');
            $table->dropColumn('TipoDocumento');
            $table->dropColumn('CondicionVenta');
            $table->dropColumn('PlazoCredito');
            $table->dropColumn('CodigoMoneda');
        });
    }
}
