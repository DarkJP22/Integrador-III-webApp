<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->after('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });
    }
};
