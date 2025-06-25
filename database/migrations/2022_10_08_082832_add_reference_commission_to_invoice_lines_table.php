<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->tinyInteger('reference_commission')->default(0)->after('is_servicio_medico');
            $table->tinyInteger('no_aplica_commission')->default(0)->after('reference_commission');
            $table->decimal('total_commission', 18, 5)->default(0)->after('no_aplica_commission');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropColumn('total_commission');
            $table->dropColumn('no_aplica_commission');
            $table->dropColumn('reference_commission');
        });
    }
};
