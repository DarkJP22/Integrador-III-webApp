<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exam_packages', function (Blueprint $table) {
            $table->foreignId('office_id')->after('amount')->nullable()->index();
        });

        \Illuminate\Support\Facades\DB::table('exam_packages')->update(['office_id' => 8]);
    }

    public function down(): void
    {
        Schema::table('exam_packages', function (Blueprint $table) {
            $table->dropColumn('office_id');
        });
    }
};
