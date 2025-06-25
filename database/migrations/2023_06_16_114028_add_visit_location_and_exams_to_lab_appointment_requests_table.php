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
        Schema::table('lab_appointment_requests', function (Blueprint $table) {
            $table->string('visit_location')->after('coords')->nullable();
            $table->text('exams')->after('coords')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_appointment_requests', function (Blueprint $table) {
            //
        });
    }
};
