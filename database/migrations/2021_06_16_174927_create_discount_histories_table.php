<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('created_by')->unsigned()->index();
            $table->char('CodigoMoneda', 3)->default('CRC');
            $table->decimal('amount', 18, 5)->default(0);
            $table->decimal('discount', 4, 2)->default(0);
            $table->decimal('total_discount', 18, 5)->default(0);
            $table->decimal('total', 18, 5)->default(0);
            $table->text('description')->nullable();  
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
        Schema::dropIfExists('discount_histories');
    }
}
