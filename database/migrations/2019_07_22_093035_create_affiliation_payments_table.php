<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliationPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliation_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('affiliation_id')->unsigned()->index();
            $table->text('detail');
            $table->dateTime('date');
            $table->decimal('amount', 18, 5)->default(0);
            $table->string('payment_way')->nullable();
            $table->string('code_transaction')->nullable();
            $table->string('CodigoMoneda')->default('CRC');
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
        Schema::dropIfExists('affiliation_payments');
    }
}
