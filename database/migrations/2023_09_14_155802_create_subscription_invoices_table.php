<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sequence_number')->nullable();
            $table->bigInteger('customer_sequence_number')->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('invoice_number');
            $table->string('reference_number')->nullable();
            $table->tinyInteger('paid_status')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('discount', 15,2)->nullable();
            $table->double('discount_val')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('total')->nullable();
            $table->double('tax')->nullable();
            $table->foreignId('currency_id')->nullable()->default(1)->index();
            $table->unsignedInteger('creator_id')->nullable()->index();
            $table->unsignedInteger('customer_id')->nullable()->index();

            $table->timestamps();
        });

        Schema::create('subscription_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_invoice_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type');
            $table->double('price');
            $table->decimal('quantity', 15,2);
            $table->string('unit_name')->nullable();
            $table->decimal('discount', 15,2)->nullable();
            $table->double('discount_val')->default(0);
            $table->double('tax')->nullable();
            $table->double('total')->nullable();

            $table->timestamps();
        });

        Schema::create('subscription_invoice_item_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_invoice_id')->constrained(
                table: 'subscription_invoices', indexName: 'subscription_invoices_invoice_id'
            )->cascadeOnDelete();
            $table->foreignId('subscription_invoice_item_id')->constrained(
                table: 'subscription_invoice_items', indexName: 'subscription_invoices_items_invoice_item_id'
            )->cascadeOnDelete();
            $table->string('name');
            $table->double('amount');
            $table->decimal('percent', 5,2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoice_item_taxes');
        Schema::dropIfExists('subscription_invoice_items');
        Schema::dropIfExists('subscription_invoices');
    }
};
