<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceLineDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('invoice_line_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_line_id')->index();
            $table->decimal('PorcentajeDescuento', 4, 2);
            $table->decimal('MontoDescuento', 18, 5);
            $table->string('NaturalezaDescuento');
          
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_line_discounts');
    }
}
