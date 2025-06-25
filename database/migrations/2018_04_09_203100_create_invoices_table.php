<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->index();
            $table->integer('created_by')->unsigned()->index(); // user
            $table->integer('user_id')->unsigned()->index(); // medico
            $table->integer('office_id')->unsigned()->index(); // clinica
            $table->integer('appointment_id')->default(0);// si es por cita
            $table->integer('consecutivo');
            $table->string('tipo_identificacion_cliente')->nullable();
            $table->string('identificacion_cliente')->nullable();
            $table->string('cliente')->nullable();
            $table->string('email')->nullable();
            $table->char('TipoDocumento')->default('01'); //
            $table->char('MedioPago', 2)->default('01');
            $table->char('CondicionVenta', 2)->default('01');
            $table->string('PlazoCredito')->nullable();
            $table->char('CodigoMoneda', 3)->default('CRC'); //
            $table->decimal('TipoCambio', 18, 5)->default(1);
            $table->decimal('TotalServGravados', 18, 5)->default(0);
            $table->decimal('TotalServExentos', 18, 5)->default(0);
            $table->decimal('TotalMercanciasGravadas', 18, 5)->default(0);
            $table->decimal('TotalMercanciasExentas', 18, 5)->default(0);
            $table->decimal('TotalGravado', 18, 5)->default(0);
            $table->decimal('TotalExento', 18, 5)->default(0);
            $table->decimal('TotalVenta', 18, 5)->default(0);
            $table->decimal('TotalDescuentos', 18, 5)->default(0);
            $table->decimal('TotalVentaNeta', 18, 5)->default(0);
            $table->decimal('TotalImpuesto', 18, 5)->default(0);
            $table->decimal('TotalComprobante', 18, 5)->default(0);
            $table->decimal('TotalWithNota', 18, 5)->default(0);
            $table->decimal('pay_with', 18, 5)->default(0);
            $table->decimal('change', 18, 5)->default(0);
            $table->tinyInteger('status')->default(0); //1 facturada
            $table->integer('obligado_tributario_id')->unsigned()->index();
            $table->integer('sucursal')->default(1);
            $table->integer('pos')->default(1);
            $table->string('NumeroConsecutivo')->nullable(); // con hacienda
            $table->string('clave_fe')->nullable();
            $table->string('status_fe')->nullable();
            $table->text('resp_hacienda')->nullable();
            $table->tinyInteger('sent_to_hacienda')->default(0);
            $table->tinyInteger('created_xml')->default(0);
            $table->tinyInteger('fe')->default(0); //utiliza factura electronica
            $table->unsignedInteger('discount_id')->index();
            $table->timestamps();
           
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id')->index();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->string('Codigo');
            $table->char('CodigoTipo', 2)->default('01');
            $table->string('Detalle');
            $table->decimal('Cantidad', 16, 3)->default(0);
            $table->string('UnidadMedida');
            $table->decimal('PrecioUnitario', 18, 5)->default(0);
            $table->decimal('MontoTotal', 18, 5)->default(0);
            $table->decimal('PorcentajeDescuento', 4, 2)->default(0);
            $table->decimal('MontoDescuento', 18, 5)->default(0);
            $table->string('NaturalezaDescuento')->nullable();
            $table->decimal('SubTotal', 18, 5)->default(0);
            $table->decimal('totalTaxes', 18, 5)->default(0);
            $table->decimal('MontoTotalLinea', 18, 5)->default(0);
            $table->tinyInteger('updateStock')->default(1); //1 actualizar inventario
            $table->tinyInteger('exo')->default(0); //1 actualizar inventario
            $table->char('type', 2)->default('S');
            $table->timestamps();
        });

        Schema::create('invoice_line_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_line_id')->index();
            $table->char('code', 2)->default('01');
            $table->decimal('tarifa', 4, 2);
            $table->string('amount');
        });

        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_line_taxes');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
    }
}
