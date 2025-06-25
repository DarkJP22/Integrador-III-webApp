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
        Schema::table('offices', function (Blueprint $table) {
            $table->string('whatsapp_number')->after('phone')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->after('phone_number')->nullable();
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->string('whatsapp_number')->after('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }
};
