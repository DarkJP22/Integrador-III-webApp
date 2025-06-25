<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->text('comprobante')->after('customer_id')->nullable();
            $table->date('paid_at')->after('comprobante')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            //
        });
    }
};
