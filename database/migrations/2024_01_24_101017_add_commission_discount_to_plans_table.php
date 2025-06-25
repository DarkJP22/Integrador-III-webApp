<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('commission_discount',4,2)->default(0);
            $table->integer('commission_discount_range_in_minutes')->default(0);
        });
    }

    public function down(): void
    {
    }
};
