<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('phone_number_2')->after('phone_country_code')->nullable();
            $table->string('phone_country_code_2')->after('phone_number_2')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('phone_number_2');
            $table->dropColumn('phone_country_code_2');
        });
    }
};
