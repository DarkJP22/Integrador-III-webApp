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
        Schema::table('orders', function (Blueprint $table) {
            // Modificar campos existentes que necesitan cambios - no agregar unique ya existe
            $table->string('status')->default('cotizacion')->change();
            $table->boolean('payment_method')->default(0)->change();
            $table->boolean('requires_shipping')->default(false)->change();
            $table->text('address')->nullable()->change();
            $table->decimal('lat', 10, 8)->nullable()->change();
            $table->decimal('lot', 11, 8)->nullable()->change();
            $table->decimal('order_total', 10, 2)->default(0)->change();
            $table->decimal('shipping_total', 10, 2)->default(0)->change();
            $table->longText('voucher')->nullable()->change();
            
            // Los índices ya se crean en la migración de creación de tabla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Los índices se manejan en la migración de creación de tabla
            
            // Revertir cambios de tipos de datos - no tocar unique ya existe
            $table->string('status')->change();
            $table->boolean('payment_method')->change();
            $table->boolean('requires_shipping')->change();
            $table->string('address')->nullable()->change();
            $table->double('lat')->nullable()->change();
            $table->double('lot')->nullable()->change();
            $table->float('order_total')->nullable()->change();
            $table->float('shipping_total')->nullable()->change();
            $table->binary('voucher')->nullable()->change();
        });
    }
};
