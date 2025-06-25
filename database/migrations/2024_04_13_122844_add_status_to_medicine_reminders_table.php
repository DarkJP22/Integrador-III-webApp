<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medicine_reminders', function (Blueprint $table) {
            $table->tinyInteger('status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('medicine_reminders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
