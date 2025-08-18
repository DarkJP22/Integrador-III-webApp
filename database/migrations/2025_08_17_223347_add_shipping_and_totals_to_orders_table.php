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
            // Agregar campos para cálculos de totales
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_total')->comment('Costo específico del envío');
            $table->decimal('products_subtotal', 10, 2)->default(0)->after('shipping_cost')->comment('Subtotal de productos sin IVA');
            $table->decimal('iva_total', 10, 2)->default(0)->after('products_subtotal')->comment('Total del IVA calculado');
            $table->decimal('products_total', 10, 2)->default(0)->after('iva_total')->comment('Total de productos con IVA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Eliminar los campos agregados
            $table->dropColumn(['shipping_cost', 'products_subtotal', 'iva_total', 'products_total']);
        });
    }
};
