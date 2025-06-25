<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBaseImponibleToInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->decimal('BaseImponible', 18, 5)->default(0)->after('SubTotal');
        });

        // Schema::table('proforma_lines', function (Blueprint $table) {
        //     $table->decimal('BaseImponible', 18, 5)->default(0)->after('SubTotal');
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
            $table->dropColumn('BaseImponible');
        });

        // Schema::table('proforma_lines', function (Blueprint $table) {
        //     $table->dropColumn('BaseImponible');
        // });
    }
}
