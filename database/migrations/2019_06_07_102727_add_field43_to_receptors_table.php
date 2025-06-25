<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddField43ToReceptorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receptors', function (Blueprint $table) {
            $table->string('CodigoActividad')->nullable();
            $table->char('CondicionImpuesto', 2)->nullable();
            $table->decimal('MontoTotalImpuestoAcreditar', 18, 5)->nullable()->default(0);
            $table->decimal('MontoTotalDeGastoAplicable', 18, 5)->nullable()->default(0);
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
            $table->dropColumn('CodigoActividad');
            $table->dropColumn('CondicionImpuesto');
            $table->dropColumn('MontoTotalImpuestoAcreditar');
            $table->dropColumn('MontoTotalDeGastoAplicable');
        });
    }
}
