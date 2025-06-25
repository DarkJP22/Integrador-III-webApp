<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalPaymentsToCierresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cierres', function (Blueprint $table) {
            $table->decimal('TotalPayments', 18, 5)->default(0);
            $table->decimal('TotalPaymentsDolar', 18, 5)->default(0);

            $table->decimal('TotalPaymentsCurrentCxcs', 18, 5)->default(0);
            $table->decimal('TotalPaymentsCurrentCxcsDolar', 18, 5)->default(0);

            $table->decimal('TotalAbonos', 18, 5)->default(0);
            $table->decimal('TotalAbonosDolar', 18, 5)->default(0);

            $table->decimal('TotalCxc', 18, 5)->default(0);
            $table->decimal('TotalCxcDolar', 18, 5)->default(0);

            $table->text('observations')->nullable();
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
            $table->dropColumn('TotalPayments');
            $table->dropColumn('TotalPaymentsDolar');
            $table->dropColumn('TotalPaymentsCurrentCxcs');
            $table->dropColumn('TotalPaymentsCurrentCxcsDolar');
            $table->dropColumn('TotalAbonos');
            $table->dropColumn('TotalAbonosDolar');
            $table->dropColumn('TotalCxc');
            $table->dropColumn('TotalCxcDolar');
            $table->dropColumn('observations');
        });
    }
}
