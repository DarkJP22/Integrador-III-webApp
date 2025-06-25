<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsExoIvaToCierresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cierres', function (Blueprint $table) {
            $table->decimal('TotalExonerado', 18, 5)->default(0);
            $table->decimal('TotalExoneradoDolar', 18, 5)->default(0);
            $table->decimal('TotalIVADevuelto', 18, 5)->default(0);
            $table->decimal('TotalIVADevueltoDolar', 18, 5)->default(0);
            $table->decimal('TotalDescuento', 18, 5)->default(0);
            $table->decimal('TotalDescuentoDolar', 18, 5)->default(0);
            $table->decimal('TotalImpuesto', 18, 5)->default(0);
            $table->decimal('TotalImpuestoDolar', 18, 5)->default(0);
            $table->integer('TotalFacturas')->default(0);
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
            $table->dropColumn('TotalExonerado');
            $table->dropColumn('TotalExoneradoDolar');
            $table->dropColumn('TotalIVADevuelto');
            $table->dropColumn('TotalIVADevueltoDolar');
            $table->dropColumn('TotalDescuento');
            $table->dropColumn('TotalDescuentoDolar');
            $table->dropColumn('TotalImpuesto');
            $table->dropColumn('TotalImpuestoDolar');
            $table->dropColumn('TotalFacturas');
        });
    }
}
