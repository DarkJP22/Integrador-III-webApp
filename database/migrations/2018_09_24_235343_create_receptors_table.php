<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceptorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receptors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('created_by')->unsigned()->index(); // user
            $table->integer('consecutivo');
            $table->string('tipo_identificacion_emisor');
            $table->string('NumeroCedulaEmisor');
            $table->string('tipo_identificacion_receptor');
            $table->string('NumeroCedulaReceptor');
            $table->char('Mensaje')->default('1'); //
            $table->string('DetalleMensaje')->nullable(); //
            $table->decimal('MontoTotalImpuesto', 18, 5)->default(0);
            $table->decimal('TotalFactura', 18, 5)->default(0);
            $table->tinyInteger('status')->default(0); //1 facturada
            $table->integer('obligado_tributario_id')->unsigned()->index();
            $table->integer('sucursal')->default(1);
            $table->integer('pos')->default(1);
            $table->string('NumeroConsecutivoReceptor')->nullable(); // con hacienda
            $table->string('Clave')->nullable();
            $table->string('Clave_Mensaje')->nullable();
            $table->text('resp_hacienda')->nullable();
            $table->string('status_fe')->nullable();
            $table->tinyInteger('sent_to_hacienda')->default(0);
            $table->tinyInteger('created_xml')->default(0);
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
        Schema::dropIfExists('receptors');
    }
}
