<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCierresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierres', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('office_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('created_by')->index();
            $table->integer('Facturas_finalizadas')->default(0);
            $table->decimal('TotalCreditoDolar', 18, 5)->default(0);
            $table->decimal('TotalCredito', 18, 5)->default(0);
            $table->decimal('TotalContadoDolar', 18, 5)->default(0);
            $table->decimal('TotalContado', 18, 5)->default(0);
            $table->decimal('TotalEfectivoDolar', 18, 5)->default(0);
            $table->decimal('TotalEfectivo', 18, 5)->default(0);
            $table->decimal('TotalTarjetaDolar', 18, 5)->default(0);
            $table->decimal('TotalTarjeta', 18, 5)->default(0);
            $table->decimal('TotalChequeDolar', 18, 5)->default(0);
            $table->decimal('TotalCheque', 18, 5)->default(0);
            $table->decimal('TotalDepositoDolar', 18, 5)->default(0);
            $table->decimal('TotalDeposito', 18, 5)->default(0);
            $table->decimal('TotalLaboratorioDolar', 18, 5)->default(0);
            $table->decimal('TotalLaboratorio', 18, 5)->default(0);
            $table->decimal('TotalClinicaDolar', 18, 5)->default(0);
            $table->decimal('TotalClinica', 18, 5)->default(0);
            $table->decimal('TotalVentasDolar', 18, 5)->default(0);
            $table->decimal('TotalVentas', 18, 5)->default(0);
            $table->decimal('TotalExentoDolar', 18, 5)->default(0);
            $table->decimal('TotalExento', 18, 5)->default(0);
            $table->decimal('TotalGravadoDolar', 18, 5)->default(0);
            $table->decimal('TotalGravado', 18, 5)->default(0);
            $table->dateTime('from');
            $table->dateTime('to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cierres');
    }
}
