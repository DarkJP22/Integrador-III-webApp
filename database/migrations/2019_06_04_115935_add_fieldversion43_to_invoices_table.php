<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldversion43ToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('TotalServExonerado', 18, 5)->default(0);
            $table->decimal('TotalMercExonerada', 18, 5)->default(0);
            $table->decimal('TotalExonerado', 18, 5)->default(0);
            $table->decimal('TotalIVADevuelto', 18, 5)->default(0);
        });

        // Schema::table('proformas', function (Blueprint $table) {
        //     $table->decimal('TotalServExonerado', 18, 5)->default(0);
        //     $table->decimal('TotalMercExonerada', 18, 5)->default(0);
        //     $table->decimal('TotalExonerado', 18, 5)->default(0);
        //     $table->decimal('TotalIVADevuelto', 18, 5)->default(0);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('TotalServExonerado');
            $table->dropColumn('TotalMercExonerada');
            $table->dropColumn('TotalExonerado');
            $table->dropColumn('TotalIVADevuelto');
        });

        // Schema::table('proformas', function (Blueprint $table) {
        //     $table->dropColumn('TotalServExonerado');
        //     $table->dropColumn('TotalMercExonerada');
        //     $table->dropColumn('TotalExonerado');
        //     $table->dropColumn('TotalIVADevuelto');
        // });
    }
}
