<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('commission_by_appointment')->default(false);
            $table->decimal('general_cost_commission_by_appointment', 18, 5)->default(0);
            $table->decimal('specialist_cost_commission_by_appointment', 18, 5)->default(0);
            $table->foreignId('currency_id')->nullable()->default(1)->index();
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
