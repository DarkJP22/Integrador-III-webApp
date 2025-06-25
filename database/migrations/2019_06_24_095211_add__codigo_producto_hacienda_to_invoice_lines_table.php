<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodigoProductoHaciendaToInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->string('CodigoProductoHacienda')->nullable()->after('CodigoTipo');
        });
        // Schema::table('proforma_lines', function (Blueprint $table) {
        //     $table->string('CodigoProductoHacienda')->nullable()->after('CodigoTipo');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropColumn('CodigoProductoHacienda');
        });
        // Schema::table('proforma_lines', function (Blueprint $table) {
        //     $table->dropColumn('CodigoProductoHacienda');
        // });
    }
}
