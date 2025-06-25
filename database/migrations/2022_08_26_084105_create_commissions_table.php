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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->decimal('Total', 18, 5)->default(0);
            $table->string('CodigoMoneda')->default('CRC');
            $table->timestamp('paid_at')->nullable();
            $table->text('comprobante')->nullable();
            $table->timestamps();
        });
        Schema::create('commission_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->nullableMorphs('resource');
            $table->decimal('MontoTransaccion', 18, 5)->default(0);
            $table->decimal('Porcentaje', 4, 2)->default(0);
            $table->decimal('Total', 18, 5)->default(0);
            $table->char('CodigoMoneda', 3)->default('CRC');
            $table->timestamp('paid_at')->nullable();
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
        Schema::dropIfExists('commissions');
    }
};
