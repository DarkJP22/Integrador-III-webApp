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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('canton')->after('province')->nullable();
            $table->string('district')->after('canton')->nullable();
            $table->string('birth_date')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('phone_country_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            //
        });
    }
};
