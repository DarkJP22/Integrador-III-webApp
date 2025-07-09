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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('drug_id')->constrained()->onDelete('cascade');
            $table->integer('requested_amount')->default(1);
            $table->integer('quantity_available')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->decimal('products_total', 10, 2)->default(0);
            $table->timestamps();
            
            // Índices para mejor performance
            $table->index('order_id');
            $table->index('drug_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
