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
        Schema::table('order_details', function (Blueprint $table) {
            // Modificar campos existentes
            $table->integer('requested_amount')->default(1)->change();
            $table->integer('quantity_available')->default(0)->change();
            $table->decimal('unit_price', 10, 2)->default(0)->change();
            $table->text('description')->nullable()->change();
            $table->decimal('products_total', 10, 2)->default(0)->change();
            
            // Los índices ya se crean en la migración de creación de tabla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Los índices se manejan en la migración de creación de tabla
            
            // Revertir tipos de datos
            $table->integer('requested_amount')->change();
            $table->integer('quantity_available')->change();
            $table->decimal('unit_price', 10, 2)->change();
            $table->string('description')->nullable()->change();
            $table->float('products_total')->change();
        });
    }
};
