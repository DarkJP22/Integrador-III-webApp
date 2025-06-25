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
        Schema::table('patient_codes', function (Blueprint $table) {
            $table->boolean('was_used')->default(false);
        });
        Schema::table('patient_user', function (Blueprint $table) {
            $table->string('authorization_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_codes', function (Blueprint $table) {
            $table->dropColumn('was_used');
        });
        Schema::table('patient_user', function (Blueprint $table) {
            $table->dropColumn('authorization_code');
        });
    }
};
