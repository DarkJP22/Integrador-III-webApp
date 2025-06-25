<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsExonerationToInvoiceLineTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_line_taxes', function (Blueprint $table) {
            $table->char('TipoDocumento', 2)->default('01')->nullable();
            $table->string('NumeroDocumento')->nullable();
            $table->string('NombreInstitucion')->nullable();
            $table->datetime('FechaEmision')->nullable();
            $table->decimal('PorcentajeExoneracion', 5, 2)->nullable();
            $table->decimal('MontoExoneracion', 18, 5)->nullable();
            $table->decimal('ImpuestoOriginal', 18, 5)->nullable();
            $table->decimal('ImpuestoNeto', 18, 5)->nullable();
            $table->string('name')->nullable();
            $table->decimal('TarifaOriginal', 18, 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_line_taxes', function (Blueprint $table) {
            $table->dropColumn('TipoDocumento');
            $table->dropColumn('NumeroDocumento');
            $table->dropColumn('NombreInstitucion');
            $table->dropColumn('FechaEmision');
            $table->dropColumn('PorcentajeExoneracion');
            $table->dropColumn('MontoExoneracion');
            $table->dropColumn('ImpuestoOriginal');
            $table->dropColumn('ImpuestoNeto');
            $table->dropColumn('name');
            $table->dropColumn('TarifaOriginal');
        });
    }
}
