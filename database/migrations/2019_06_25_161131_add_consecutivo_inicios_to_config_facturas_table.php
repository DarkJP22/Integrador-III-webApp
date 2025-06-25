<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsecutivoIniciosToConfigFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config_facturas', function (Blueprint $table) {
            $table->integer('consecutivo_inicio_tiquete')->default(1)->after('consecutivo_inicio_NC');
            $table->integer('consecutivo_inicio_receptor')->default(1)->after('consecutivo_inicio_NC');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config_facturas', function (Blueprint $table) {
            $table->dropColumn('consecutivo_inicio_tiquete');
            $table->dropColumn('consecutivo_inicio_receptor');
        });
    }
}
