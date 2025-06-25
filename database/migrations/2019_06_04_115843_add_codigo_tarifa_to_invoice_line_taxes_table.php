<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodigoTarifaToInvoiceLineTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_line_taxes', function (Blueprint $table) {
            $table->char('CodigoTarifa', 2)->nullable();
        });
        // Schema::table('proforma_line_taxes', function (Blueprint $table) {
        //     $table->char('CodigoTarifa', 2)->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_line_taxes', function (Blueprint $table) {
            $table->dropColumn('CodigoTarifa');
        });

        // Schema::table('proforma_line_taxes', function (Blueprint $table) {
        //     $table->dropColumn('CodigoTarifa');
        // });
    }
}
