<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAuthorizationApiToConfigFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config_facturas', function (Blueprint $table) {
            $table->string('grant_type')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('refresh_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config_facturas', function (Blueprint $table) {
            $table->dropColumn('grant_type');
            $table->dropColumn('access_token');
            $table->dropColumn('refresh_token');
            $table->dropColumn('token_expires_at');
            $table->dropColumn('refresh_expires_at');
        });
    }
}
