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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('consecutive')->unique();
            $table->unsignedInteger('pharmacy_id'); 
            $table->unsignedInteger('user_id');  
            $table->dateTime('date');
            $table->string('status')->default('cotizacion');
            $table->boolean('payment_method')->default(0); // 0=efectivo, 1=sinpe
            $table->boolean('requires_shipping')->default(false);
            $table->text('address')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lot', 11, 8)->nullable(); 
            $table->decimal('order_total', 10, 2)->default(0);
            $table->decimal('shipping_total', 10, 2)->default(0);
            $table->longText('voucher')->nullable(); 
            $table->timestamps();
            
            // Foreign keys con el tipo correcto
            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para mejor performance
            $table->index(['pharmacy_id', 'status']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
