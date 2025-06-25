<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pressures', function (Blueprint $table) {
            $table->string('measurement_place')->nullable();
            $table->text('observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pressures', function (Blueprint $table) {
            $table->dropColumn('observations');
            $table->dropColumn('measurement_place');
        });
    }
};
