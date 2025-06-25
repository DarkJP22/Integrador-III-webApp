<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliationTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliation_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('affiliation_id')->unsigned()->index();
            $table->integer('transactable_id')->unsigned()->index();
            $table->string('transactable_type');
            $table->decimal('MontoTransaccion', 18, 5)->default(0);
            $table->decimal('AcumuladoAntesTransaccion', 18, 5)->default(0);
            $table->decimal('AcumuladoDespuesTransaccion', 18, 5)->default(0);
            $table->string('action')->nullable();
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
        Schema::dropIfExists('affiliation_transactions');
    }
}
