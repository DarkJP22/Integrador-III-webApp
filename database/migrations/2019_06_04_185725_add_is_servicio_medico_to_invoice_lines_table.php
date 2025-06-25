<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsServicioMedicoToInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->tinyInteger('is_servicio_medico')->default(0);
        });

        // Schema::table('proforma_lines', function (Blueprint $table) {
        //     $table->tinyInteger('is_servicio_medico')->default(0);
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
            $table->dropColumn('is_servicio_medico');
        });

        // Schema::table('proforma_lines', function (Blueprint $table) {
        //     $table->dropColumn('is_servicio_medico');
        // });
    }
}
