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
        Schema::table('labresults', function (Blueprint $table) {
            $table->unsignedInteger('medic_id')->nullable();
            $table->timestamp('read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labresults', function (Blueprint $table) {
            $table->dropColumn('medic_id');
            $table->dropColumn('read_at');
        });
    }
};
