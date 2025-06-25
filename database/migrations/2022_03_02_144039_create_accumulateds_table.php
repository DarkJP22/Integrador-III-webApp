<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccumulatedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accumulateds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('patient_id')->nullable()->index();
            $table->timestamp('active_at')->nullable();
            $table->decimal('acumulado', 18, 5)->default(0);
            $table->string('CodigoMoneda')->default('CRC');
            $table->timestamps();
        });

        Schema::create('accumulated_patient', function (Blueprint $table) {
            $table->foreignId('accumulated_id')->index();
            $table->foreignId('patient_id')->index();

            $table->unique(['accumulated_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accumulated_patient');
        Schema::dropIfExists('accumulateds');
    }
}
