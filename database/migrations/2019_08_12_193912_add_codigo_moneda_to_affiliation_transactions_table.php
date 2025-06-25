<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodigoMonedaToAffiliationTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliation_transactions', function (Blueprint $table) {
            $table->string('CodigoMoneda')->default('CRC');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliation_transactions', function (Blueprint $table) {
            $table->dropColumn('CodigoMoneda');
        });
    }
}
