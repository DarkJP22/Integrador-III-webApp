<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccumulatedTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accumulated_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accumulated_id')->index();
            $table->nullableMorphs('resource');
            $table->decimal('MontoTransaccion', 18, 5)->default(0);
            $table->decimal('AcumuladoAntesTransaccion', 18, 5)->default(0);
            $table->decimal('AcumuladoDespuesTransaccion', 18, 5)->default(0);
            $table->string('action')->nullable();
            $table->char('CodigoMoneda', 3)->default('CRC');
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
        Schema::dropIfExists('accumulated_transactions');
    }
}
