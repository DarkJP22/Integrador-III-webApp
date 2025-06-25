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
        Schema::table('quote_orders', function (Blueprint $table) {
            $table->foreignId('office_id')->after('user_id')->nullable()->index();
        });

        \Illuminate\Support\Facades\DB::table('quote_orders')->update(['office_id' => 8]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_orders', function (Blueprint $table) {
            $table->dropColumn('office_id');
        });
    }
};
